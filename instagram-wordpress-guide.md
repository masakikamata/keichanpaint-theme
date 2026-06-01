# インスタグラム動画をWordPressに自動投稿する方法
## 〜素人向け・完全手順ガイド〜

---

## 全体の流れ

```
Instagram (Meta) のAPI取得
        ↓
WordPress にプラグイン設定
        ↓
自動投稿スタート！
```

---

## STEP 1：Facebookビジネスアカウントの準備

Instagram APIは **Meta（Facebook）** が管理しています。
まずFacebook側での準備が必要です。

### 1-1. Facebookページを作る（まだない場合）
1. [https://www.facebook.com](https://www.facebook.com) にログイン
2. 左メニューから「**ページ**」をクリック
3. 「**新しいページを作成**」をクリック
4. ページ名・カテゴリを入力して作成

### 1-2. InstagramをFacebookページに連携する
1. Instagramアプリを開く
2. 右上のメニュー（三本線）→「**設定とアクティビティ**」
3. 「**アカウント**」→「**プロフェッショナルアカウントに切り替える**」
4. 「ビジネス」を選択
5. 「**Facebookページにリンク**」で先ほど作ったページと連携

---

## STEP 2：Meta for Developersでアプリを作る

### 2-1. Metaデベロッパーサイトにアクセス

👉 [https://developers.facebook.com](https://developers.facebook.com)

1. 右上の「**ログイン**」からFacebookアカウントでログイン
2. 「**マイアプリ**」をクリック
3. 「**アプリを作成**」ボタンをクリック

### 2-2. アプリの種類を選ぶ

| 選択肢 | 説明 |
|--------|------|
| ビジネス | ✅ **これを選ぶ** |
| コンシューマー | 個人向け |
| その他 | 特殊用途 |

1. 「**ビジネス**」を選択して「次へ」
2. アプリ名を入力（例：`my-instagram-wp`）
3. メールアドレスを確認して「**アプリを作成**」

### 2-3. Instagram Graph API を追加する

1. アプリダッシュボードの左メニューから「**製品を追加**」
2. 「**Instagram Graph API**」の「**設定**」をクリック

---

## STEP 3：アクセストークン（APIキー）を取得する

### 3-1. アクセストークンとは？
> インスタの情報にアクセスするための「**合言葉（パスワードのようなもの）**」です。

### 3-2. トークンを発行する手順

1. 左メニュー「**ツール**」→「**グラフAPIエクスプローラ**」をクリック
2. 右上の「**アクセストークンを生成**」をクリック
3. 自分のFacebookページを選択
4. 以下の権限にチェックを入れる：

   - ✅ `instagram_basic`
   - ✅ `instagram_content_publish`
   - ✅ `pages_show_list`
   - ✅ `pages_read_engagement`

5. 「**アクセストークンを生成**」→「**許可する**」をクリック
6. 画面に長い文字列が表示される → これが**アクセストークン**

```
例: EAABxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

⚠️ **このトークンは他人に見せないこと！**

### 3-3. 長期トークンに変換する（重要！）

デフォルトのトークンは **60日で期限切れ** になります。
長期（無期限に近い）トークンに変換しましょう。

1. 左メニュー「**ツール**」→「**アクセストークンデバッガ**」
2. トークンを貼り付けて「**デバッグ**」
3. 「**長期トークンに延長**」をクリック
4. 新しいトークンをコピーして保存

---

## STEP 4：Instagram ユーザーIDを取得する

### 4-1. ユーザーIDとは？
> Instagramアカウントの「**番号（ID）**」です。ユーザー名とは別物。

### 4-2. 取得方法

1. グラフAPIエクスプローラに戻る
2. 上部の入力欄に以下を入力して「**送信**」をクリック：

```
me/accounts
```

3. 表示されたJSONの中に `id` という数字が出てくる → これが **Facebookページ ID**

4. 次に以下を入力して「**送信**」：

```
{FacebookページID}?fields=instagram_business_account
```

例：
```
123456789012345?fields=instagram_business_account
```

5. 表示される `id` の数字 → これが **Instagram ビジネスアカウントID**

```json
{
  "instagram_business_account": {
    "id": "17841400000000000"  ← これがInstagram ID
  }
}
```

---

## STEP 5：WordPressプラグインで自動投稿を設定する

### おすすめプラグイン（無料）

| プラグイン名 | 特徴 |
|-------------|------|
| **Smash Balloon Social Photo Feed** | 設定が簡単・日本語OK |
| **Instagram Feed** | シンプル |
| **WP Social Ninja** | 多機能 |

### Smash Balloon の設定手順（最もおすすめ）

1. WordPress管理画面 → 「**プラグイン**」→「**新規追加**」
2. 検索欄に「`Smash Balloon`」と入力
3. 「**今すぐインストール**」→「**有効化**」
4. 左メニューに「**Instagram Feed**」が追加される
5. 「**Instagram Feedを設定**」をクリック
6. 「**Instagramに接続**」→ Instagramでログイン
7. 先ほど取得した **アクセストークン** と **Instagram ID** を貼り付け
8. 「**保存**」

### 投稿先のページに表示させる

投稿したいWordPressページの編集画面を開いて、
以下の **ショートコード** を貼り付けるだけ：

```
[instagram-feed]
```

---

## STEP 6：自動更新の設定

Smash Balloonは標準で **1時間ごと** に自動更新されます。

更新頻度を変えたい場合：
1. 「**Instagram Feed**」→「**カスタマイズ**」
2. 「**フィードの設定**」→「**キャッシュ時間**」
3. 好みの時間に変更

---

## よくあるトラブルと解決策

| 問題 | 原因 | 解決策 |
|------|------|--------|
| 動画が表示されない | トークンの権限不足 | `instagram_basic` の権限を確認 |
| エラー「無効なトークン」 | トークン期限切れ | STEP 3-3 で長期トークンに変換 |
| 投稿が更新されない | キャッシュが古い | プラグインの「キャッシュをクリア」 |
| プロ限定機能と言われる | 無料版の制限 | 有料版か別プラグインを検討 |

---

## 必要なものまとめ

- [ ] Facebookアカウント
- [ ] Facebookページ（ビジネス用）
- [ ] Instagramビジネスアカウント
- [ ] Metaデベロッパーアカウント
- [ ] アクセストークン（長期）
- [ ] Instagram ユーザーID（数字）
- [ ] WordPressサイト（管理者権限）

---

## 参考リンク

- Meta for Developers: https://developers.facebook.com
- Smash Balloon公式: https://smashballoon.com
- Instagram Graph API公式ドキュメント: https://developers.facebook.com/docs/instagram-api

---

*作成日: 2026年6月1日*
