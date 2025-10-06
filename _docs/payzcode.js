/***** 設定（Payz専用） *****/
const PZ_SHEET_NAME   = 'payz_orders';
const PZ_PROCESSED    = 'processed/payz';
const PZ_GMAIL_QUERY  = [
  'newer_than:10d',
  'subject:あべラボ',
  'subject:"購読の支払いに成功"',
  'replyto:noreply@payz.jp',
  '-label:' + PZ_PROCESSED
].join(' ');

/***** 初期設定：列フォーマット固定 *****/
function pz_setup() {
  const sh = pz_getSheet_();
  // メール列(F)・電話列(G)を文字列に固定
  sh.getRange('F2:F').setNumberFormat('@');
  sh.getRange('G2:G').setNumberFormat('@');
  // 金額と日付の書式
  sh.getRange('C2:C').setNumberFormat('0');
  sh.getRange('D2:D').setNumberFormat('yyyy/MM/dd');
}

/***** メイン処理 *****/
function pz_run() {
  const label  = pz_getOrCreateLabel_(PZ_PROCESSED);
  const sh     = pz_getSheet_();
  const ths    = GmailApp.search(PZ_GMAIL_QUERY, 0, 50);
  if (ths.length === 0) return;

  for (const th of ths) {
    for (const m of th.getMessages()) {
      const msgId = m.getId();
      try {
        const { html, plain, date } = pz_getBodies_(m);
        const text = pz_htmlToText_(html || plain);
        const rec  = pz_parse_(text);

        // 注文ID = 定期購読ID + 受信日(YYYYMMDD)
        const ymd  = Utilities.formatDate(date, 'Asia/Tokyo', 'yyyy/MM/dd');
        const orderId = `${rec.subscription_id}-${ymd.replace(/\//g,'')}`;

        // 既存チェック（重複スキップ）
        if (pz_existsOrderId_(sh, orderId)) continue;

        // 電話は必ず文字列で保持（先頭0を落とさない）
        const safePhone = rec.phone ? "'" + String(rec.phone).replace(/[^\d]/g, '') : '';

        // 行データ
        const row = [
          orderId,            // 注文ID
          rec.product_name,   // 商材名
          rec.amount_jpy,     // 金額
          ymd,                // 決済日
          rec.name,           // 購入者名
          rec.email,          // メール
          safePhone,          // 電話（テキスト強制）
          rec.address,        // 住所
          msgId,              // 元メールID
          new Date()          // 処理日時
        ];

        // appendRow()ではなくsetValues()で直接書き込み（書式保持）
        const last = sh.getLastRow();
        sh.getRange(last + 1, 1, 1, row.length).setValues([row]);
        sh.getRange(last + 1, 7).setNumberFormat('@'); // G列を再指定（安全策）
      } catch (e) {
        console.warn('payz parse error', msgId, e);
      }
    }
    th.addLabel(label);
  }
}

/***** 本文解析 *****/
function pz_parse_(txt) {
  const z2h = s => s.replace(/[０-９Ａ-Ｚａ-ｚ－￥．，：＠（）]/g, ch =>
    String.fromCharCode(ch.charCodeAt(0) - 0xFEE0)
  );
  const s = z2h(txt).replace(/\r/g,'').replace(/[ \t\u3000]+/g,' ').trim();

  const get = label => {
    const re = new RegExp(label + '\\s*([\\s\\S]*?)(?:\\n|$)');
    const m  = s.match(re);
    return m ? m[1].trim() : '';
  };

  // 電話番号（先頭0を保持）
  const raw = get('電話番号').replace(/[^\d]/g, '');
  const phone = raw.startsWith('0') ? raw : ('0' + raw);

  return {
    subscription_id: get('定期購読ID'),
    product_name:    get('商品名'),
    amount_jpy:      pz_toAmount_(get('支払い金額')),
    name:            get('名前'),
    email:           get('メールアドレス').toLowerCase(),
    phone:           phone,
    address:         get('住所')
  };
}

/***** 金額補正 *****/
function pz_toAmount_(v) {
  if (!v) return '';
  const n = String(v).replace(/[^\d.-]/g, '');
  return n ? Number(n) : '';
}

/***** 補助関数群 *****/
function pz_getSheet_() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sh = ss.getSheetByName(PZ_SHEET_NAME);
  if (!sh) throw new Error('Sheet not found: ' + PZ_SHEET_NAME);
  return sh;
}

function pz_existsOrderId_(sh, orderId) {
  const last = sh.getLastRow();
  if (last < 2) return false;
  const rng = sh.getRange(2, 1, last - 1, 1);
  const found = rng.createTextFinder(orderId).matchEntireCell(true).findNext();
  return !!found;
}

function pz_getBodies_(m) {
  return {
    html:  m.getBody() || '',
    plain: m.getPlainBody() || '',
    date:  m.getDate() || new Date()
  };
}

function pz_htmlToText_(html) {
  if (!html) return '';
  return html
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<\/p>/gi, '\n')
    .replace(/<[^>]+>/g, '')
    .replace(/\n{2,}/g, '\n')
    .trim();
}

function pz_getOrCreateLabel_(name) {
  return GmailApp.getUserLabelByName(name) || GmailApp.createLabel(name);
}
