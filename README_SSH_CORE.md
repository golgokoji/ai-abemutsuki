# 🗝️ CoreServer SSHキー管理マニュアル（AIあべむつき／abe-labo.biz用）

このドキュメントでは、**CoreServerにSSHキーを複数作成・登録・管理**する方法を説明します。  
Mac（Terminal）環境を想定しています。

---

## 🚀 SSHキーの基本

- SSHキーは「公開鍵」と「秘密鍵」のペアです。
- 1台のPCや1プロジェクトごとに**別の鍵を作っても問題ありません**。
- CoreServerは複数の鍵を同時に登録・利用できます（上限：約20個）。

---

## 1️⃣ 新しいSSHキーを作成する（Mac）

ターミナルで以下を実行：

```bash
ssh-keygen -t ed25519 -C "for_abe-labo.biz"
```

- `-t ed25519`：安全で高速な鍵方式  
- `-C "for_abe-labo.biz"`：コメント（識別名）  
- 質問はすべて **Enter** で進めてOK  
  （パスフレーズは空欄で構いません）

完了すると以下のようなメッセージが出ます：

```
Your identification has been saved in /Users/koji/.ssh/id_ed25519
Your public key has been saved in /Users/koji/.ssh/id_ed25519.pub
```

---

## 2️⃣ 公開鍵の中身をコピー

以下を実行：

```bash
cat ~/.ssh/id_ed25519.pub
```

出力された内容（例：`ssh-ed25519 AAAAC3Nza... koji@mac.local`）を**すべてコピー**します。

---

## 3️⃣ CoreServerに登録する

1. CoreServer管理画面 → 「SSH設定」  
2. 右上の「キーの貼り付け」ボタンをクリック  
3. コメント欄に以下のように入力（識別しやすい名前）  
   ```
   for_abe-labo.biz
   ```
4. 公開鍵を貼り付けて「追加」→「承認」まで実行  
5. ステータスが「承認済み（緑色）」になればOK ✅

---

## 4️⃣ SSH接続を確認する

接続コマンドを実行：

```bash
ssh -i ~/.ssh/id_ed25519 golgokoji@v2009.coreserver.jp -p 22
```

- `golgokoji` → CoreServerのアカウント名  
- `v2009.coreserver.jp` → CoreServerホスト名  
- `-p 10022` → SSHポート番号（通常22 or 10022）

成功時の例：

```
Last login: Wed Oct 8 15:08:51 2025 from 133.xxx.xxx.xxx
[golgokoji@v2009 ~]$
```

---

## 5️⃣ 複数キーを使い分ける場合

複数プロジェクトでSSHを使う場合は、  
MacのSSH設定ファイルに追記しておくと便利です。

```bash
nano ~/.ssh/config
```

以下のように設定：

```
Host coreserver
    HostName v2009.coreserver.jp
    User golgokoji
    Port 10022
    IdentityFile ~/.ssh/id_ed25519
```

保存後はこれで接続可能：

```bash
ssh coreserver
```

---

## 6️⃣ 不要な鍵の整理

CoreServerの「SSH設定」画面で、不要な鍵のチェックボックスをオン →  
「削除」ボタンで削除できます。

> 🧹 鍵を削除しても、他の有効な鍵には影響しません。

---

## 💡 運用おすすめルール

| 用途 | コメント（キー名） | 保存場所 |
|------|------------------|-----------|
| メインMac (M3) | `for_macbook_air` | `~/.ssh/id_ed25519` |
| 外出用ノート | `for_travel_mac` | `~/.ssh/id_travel` |
| GitHub Actionsデプロイ用 | `for_github_ci` | CI専用 |
| abe-labo.biz用 | `for_abe-labo.biz` | `~/.ssh/id_abe_labo` |

---

## ✅ ポイントまとめ

- SSHキーは何個でも作成可能（CoreServer上限は約20個）  
- 不要な鍵は削除してもOK（他には影響なし）  
- コメントをつけて管理すれば混乱しない  
- パスワード方式より安全・高速  
- `.ssh/config` を活用して管理を自動化すると便利

---

© Lucky Mine / AIあべむつき
