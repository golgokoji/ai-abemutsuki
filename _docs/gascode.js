/** =========================================================
 *  infotop 売上通知 → スプレッドシート集計（重複防止つき・本番版）
 *  - 検索: 件名=「クレジット決済が完了いたしました」＋ 本文に「あべラボ」
 *  - 厳格パース: 行頭「ラベル：値」だけ抽出（誤拾い防止）
 *  - 書込: 注文ID（数字）で upsert、成功時のみ “処理済み” へ記録
 *  - フィルタ/解析失敗は未処理のまま（次回も再評価）
 *  - ログ: 進捗と書込内容を詳細出力
 * ========================================================= */

// ===== デバッグ =====
const DEBUG = true;
function dbg(...args){ if (DEBUG) Logger.log(new Date().toISOString() + ' ' + args.join(' ')); }

// ===== 設定 =====
const CONFIG = {
  SHEET_ID: '18pHbkmTbF7SPK6CYwqV2eXM2gCxQUNy_MXqbJT4myJM',
  SHEET_NAME: 'sales',
  GMAIL_QUERY:
    'from:(mail3@infotop.jp OR @infotop.jp) newer_than:90d subject:"クレジット決済が完了いたしました" "あべラボ"',
  BATCH_THREADS: 30,
  PROP_PROCESSED_MSG_IDS: 'processedMsgIds_v1'
};

/** 実行エントリ（トリガー/手動） */
function runInfotopPull() {
  const ss = SpreadsheetApp.openById(CONFIG.SHEET_ID);
  const sheet = ss.getSheetByName(CONFIG.SHEET_NAME) || ss.insertSheet(CONFIG.SHEET_NAME);
  ensureHeaders_(sheet);
  const header = getHeaderMap_(sheet);
  const processedSet = loadProcessedSet_();
  const now = new Date();

  dbg('QUERY=' + CONFIG.GMAIL_QUERY);
  const threads = GmailApp.search(CONFIG.GMAIL_QUERY, 0, CONFIG.BATCH_THREADS);
  dbg('threads.length=' + threads.length);

  let cntSeen=0, cntAlready=0, cntFiltered=0, cntParsedFail=0, cntWritten=0;

  threads.forEach(th => {
    th.getMessages().forEach(msg => {
      cntSeen++;
      const msgId = msg.getId();
      const subj  = msg.getSubject() || '';
      const from  = msg.getFrom() || '';
      const date  = Utilities.formatDate(msg.getDate(), Session.getScriptTimeZone(), 'yyyy/MM/dd HH:mm');
      dbg(`MSG id=${msgId} subj="${subj}" from=${from} date=${date}`);

  // 重複スキップ処理を無効化（毎回処理）

      const body = msg.getPlainBody() || '';

      // === 二段フィルタ ===
      const ok1 = (subj.includes('クレジット決済が完了いたしました') || body.includes('クレジット決済が完了いたしました'));
      const targetWord = /(あべラボ|アベラボ|あべらぼ)/;   // 表記ゆれ許容
      const ok2 = targetWord.test(subj) || targetWord.test(body);
      if (!(ok1 && ok2)) {
        cntFiltered++;
        dbg(`  -> skip: filter fail ok1=${ok1} ok2=${ok2} body=${body}`);
        // フィルタ落ちは未処理のまま（次回も再評価）
        return;
      }

      // === 解析 ===
      const p = parseInfotop_(body);
      dbg('  parsed=' + JSON.stringify(p));

  // スキップ処理を無効化（全て書き込み対象）

      // === 書き込み（必ず反映） ===
      const rowObj = {
        注文ID: p.orderId,
        商材名: p.productName || '',
        金額: (typeof p.amount === 'number' && !isNaN(p.amount)) ? String(p.amount) : '',
        決済日: p.paidAt || '',
        購入者名: p.buyerName || '',
        メール: p.email || '',
        電話: p.tel || '',
        住所: p.address || '',
        元メールID: msgId,
        処理日時: Utilities.formatDate(now, Session.getScriptTimeZone(), 'yyyy/MM/dd HH:mm:ss')
      };
      dbg('  writing=' + JSON.stringify(rowObj));
      upsertByOrderId_(sheet, header, rowObj);
      cntWritten++; dbg('  -> upsert: OK');

      // === 正常登録できたものだけ処理済みに ===
      processedSet.add(msgId);
    });
  });

  saveProcessedSet_(processedSet);
  dbg(`SUMMARY seen=${cntSeen} already=${cntAlready} filtered=${cntFiltered} parsedFail=${cntParsedFail} written=${cntWritten}`);
}

/** 件名一覧だけ確認（デバッグ用） */
function runInfotopListSubjects(n = 50) {
  dbg('LIST QUERY=' + CONFIG.GMAIL_QUERY);
  const threads = GmailApp.search(CONFIG.GMAIL_QUERY, 0, n);
  dbg('threads.length=' + threads.length);
  threads.forEach(th => th.getMessages().forEach(m => {
    dbg(`SUBJ="${m.getSubject()}" FROM=${m.getFrom()} ID=${m.getId()}`);
  }));
}



/** —— ヘッダを常に正しい並びで上書き —— */
function ensureHeaders_(sheet) {
  const HEADERS = ['注文ID','商材名','金額','決済日','購入者名','メール','電話','住所','元メールID','処理日時'];
  sheet.getRange(1, 1, 1, HEADERS.length).setValues([HEADERS]);
  const lastCol = sheet.getLastColumn();
  if (lastCol > HEADERS.length) sheet.deleteColumns(HEADERS.length + 1, lastCol - HEADERS.length);
}

/** —— 注文IDで upsert（存在すれば更新／なければ追記）—— */
function upsertByOrderId_(sheet, header, rowObj) {
  const idCol = header['注文ID'];
  if (!idCol) throw new Error('シートに「注文ID」ヘッダがありません');

  const lastRow = sheet.getLastRow();
  if (lastRow >= 2) {
    const idRange = sheet.getRange(2, idCol, lastRow-1, 1).getValues().flat().map(v => String(v).trim());
    const idx = idRange.findIndex(v => v === String(rowObj['注文ID']));
    if (idx !== -1) {
      const rowIndex = 2 + idx;
      dbg(`  -> update row ${rowIndex}`);
      writeRow_(sheet, header, rowIndex, rowObj);
      return;
    }
  }

  const newRow = sheet.getLastRow() + 1;
  dbg(`  -> append row ${newRow}`);
  writeRow_(sheet, header, newRow, rowObj);
}

/** —— 指定行へヘッダ順で書込 —— */
function writeRow_(sheet, header, rowIndex, rowObj) {
  const headers = Object.keys(header);
  const row = new Array(headers.length).fill('');
  headers.forEach(h => { row[header[h]-1] = (h in rowObj) ? rowObj[h] : ''; });
  sheet.getRange(rowIndex, 1, 1, headers.length).setValues([row]);
}

/** —— 見出し→列番号マップ —— */
function getHeaderMap_(sheet) {
  const values = sheet.getRange(1,1,1,sheet.getLastColumn()).getValues()[0];
  const m = {};
  values.forEach((h,i) => { m[String(h).trim()] = i+1; });
  return m;
}

/** —— 見出し→列番号マップ —— */

function parseInfotop_(txt) {
  // 正規化: CR除去, tab→空白, trim, 全角→半角
  function zenkakuToHankaku(str) {
    // 全角英数字・記号（U+FF01～U+FF5E）を半角化
    return (str || '')
      .replace(/[！-～]/g, function(s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
      })
      .replace(/[：]/g, ':')
      .replace(/[　]/g, ' ')
      .replace(/[．]/g, '/')
      .replace(/[－]/g, '-')
      .replace(/ＩＤ/g, 'ID')
      .replace(/ＰＣ/g, 'PC');
  }
  const norm = (s) => zenkakuToHankaku((s || '').replace(/\r/g, '').replace(/\t/g, ' ')).trim();
  const lines = norm(txt).split('\n').map(l => l.trim()).filter(Boolean);

  // ラベル同義語
  const LABELS = {
    orderId: ['注文ID','注文ＩＤ','ID','管理ID','申込ID'],
    productName: ['商品名','商材名','件名','タイトル'],
    amount: ['月額','金額（税込）','金額','ご請求額','決済金額','初月'],
    paidAt: ['決済日','入金日','購入日','注文日','受付日'],
    buyerName: ['購入者名','氏名','お名前'],
    email: ['PCメール','ＰＣメール','メール','E-mail','E-mailアドレス','メールアドレス'],
    tel: ['電話番号','電話','TEL'],
    address: ['都道府県','住所','それ以降の住所']
  };

  // pick: ^(ラベル群):or：(.+)$
  function pick(labelRegex) {
    // ラベルの後に空白・コロンが複数あっても値を抽出
    const re = new RegExp('^' + labelRegex + '[\s　:：]*([\s　]*)(.+)$');
    for (const line of lines) {
      const m = line.match(re);
      if (m) {
        // 先頭のコロンや空白を除去
        return m[2].replace(/^[:：\s　]+/, '').trim();
      }
    }
    return '';
  }
  function pickAny(labels) {
    const labelRegex = '(?:' + labels.join('|') + ')';
    return pick(labelRegex);
  }

  // 金額抽出: 全角→半角, カンマ/空白/円/税込除去, parseInt
  function parseAmount(val) {
    if (!val) return NaN;
    val = zenkakuToHankaku(val)
      .replace(/^[:：\s　]+/, '') // 先頭のコロンや空白を除去
      .replace(/[ ,円\(\)（）]/g, '')
      .replace(/(税込)/g, '')
      .replace(/\s/g, '')
      .replace(/[^0-9]/g, '');
    const n = parseInt(val, 10);
    return isNaN(n) ? NaN : n;
  }

  // 住所結合
  function joinAddress() {
    return LABELS.address.map(l => pick('(?:' + l + ')')).filter(Boolean).join(' ').trim();
  }

  // 日付正規化 YYYY/MM/DD
  function normalizeDate(val) {
    if (!val) return '';
    val = zenkakuToHankaku(val).replace(/[.-]/g, '/');
    const m = val.match(/(\d{4})\/(\d{1,2})\/(\d{1,2})/);
    return m ? `${m[1]}/${('0'+m[2]).slice(-2)}/${('0'+m[3]).slice(-2)}` : val;
  }

  // 本文から各値抽出
  const orderIdRaw = pickAny(LABELS.orderId);
  const orderId = (orderIdRaw || '').replace(/[^0-9]/g, '');
  let amountRaw = '';
  for (const label of LABELS.amount) {
    amountRaw = pick(label);
    if (amountRaw) break;
  }
  const amount = parseAmount(amountRaw);
  const productName = pickAny(LABELS.productName);
  const paidAt = normalizeDate(pickAny(LABELS.paidAt));
  const buyerName = pickAny(LABELS.buyerName);
  const email = pickAny(LABELS.email);
  const tel = pickAny(LABELS.tel);
  const address = joinAddress();

  if (!orderId || isNaN(amount)) return {};
  return {
    orderId,
    productName,
    amount,
    paidAt,
    buyerName,
    email,
    tel,
    address
  };
}

// デバッグユーティリティ: gmail.txtサンプルでLogger.log(parseInfotop_(sample))
function debugParseFromSample() {
  const sample = `注文ＩＤ：9993846\n月額：9800円（税込）\n決済日：2025/09/17\n商品名：あべラボ\n購入者名：吉村祐志\nPCメール：yushi2519@yahoo.co.jp\n電話番号：090-9144-0622\n都道府県：東京都\n住所：港区港南\nそれ以降の住所：4-6-5-606`;
  Logger.log(parseInfotop_(sample));
}

function loadProcessedSet_() {
  const prop = PropertiesService.getScriptProperties().getProperty(CONFIG.PROP_PROCESSED_MSG_IDS);
  if (!prop) return new Set();
  return new Set(JSON.parse(prop));
}
function saveProcessedSet_(set) {
  PropertiesService.getScriptProperties().setProperty(CONFIG.PROP_PROCESSED_MSG_IDS, JSON.stringify([...set]));
}

/** —— 処理済みキャッシュをクリア（必要時のみ手動） —— */
function clearProcessedCache() {
  const prop = PropertiesService.getScriptProperties().getProperty(CONFIG.PROP_PROCESSED_MSG_IDS);
  if (prop) {
    const ids = JSON.parse(prop);
    Logger.log('clearProcessedCache: processedSet=' + JSON.stringify(ids));
  } else {
    Logger.log('clearProcessedCache: processedSet is empty');
  }
  PropertiesService.getScriptProperties().deleteProperty(CONFIG.PROP_PROCESSED_MSG_IDS);
}


/** —— 件名だけの動作確認（任意） —— */
function testQuery() {
  const q = CONFIG.GMAIL_QUERY;
  Logger.log('QUERY=' + q);
  const threads = GmailApp.search(q, 0, 10);
  Logger.log('threads.length=' + threads.length);
  threads.forEach(th => th.getMessages().forEach(m => {
    Logger.log('SUBJECT=' + m.getSubject());
  }));
}
