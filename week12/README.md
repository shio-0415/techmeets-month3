# Week12: Stripeテスト決済 + SendGrid + Stripe Webhook

## 概要
既存のLaravelアプリ（week9の会員登録機能をベースに使用）に、以下を追加実装しました。

- Stripe Checkout Sessionによるテスト決済フロー
- 決済完了後のDB保存（purchasesテーブル）
- SendGridによる会員登録完了メールの自動送信（Web API方式）
- Stripe Webhookの署名検証と決済完了ログ記録

## 主なファイル
- `app/Http/Controllers/CheckoutController.php` - 決済フロー
- `app/Http/Controllers/StripeWebhookController.php` - Webhook受信・署名検証
- `app/Mail/WelcomeMail.php` / `app/Listeners/SendWelcomeEmail.php` - 会員登録メール
- `database/migrations/2026_08_18_034134_create_purchases_table.php` - 購入履歴テーブル
- `.env.example` - 必要な環境変数のキー一覧（値は空）

## セットアップに必要な環境変数
`.env.example` を参照してください。Stripe/SendGridのテスト用APIキーが必要です。

## 補足
- メール送信は当初SMTP経由で実装しましたが、ローカル環境のアンチウイルスソフトによるSSL/TLS通信への介入が原因で証明書検証エラーが発生したため、SendGrid Web API経由（`sendgrid+api`ドライバ）に変更しました。
