# Week10 フロントエンド

## 動かし方

### バックエンド（Laravel）
\`\`\`
cd ../基本課題
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate
npm install
npm run build
\`\`\`
http://localhost:8090 でAPIが起動します。

### フロントエンド（React）
\`\`\`
npm install
npm run dev
\`\`\`
http://localhost:5173 でアクセスできます。

## コンポーネント分割の理由

投稿一覧の表示・1件ごとの投稿表示・新規作成フォームを、それぞれ PostList・PostItem・PostForm という独立したコンポーネントに分割した。理由は、それぞれが異なる役割（一覧の並べ方を決める、1件の見た目を決める、ユーザー入力を受け取って送信する）を持っており、役割ごとに分けることで、例えば投稿の見た目だけを変更したいときは PostItem だけを直せばよく、フォームの入力項目を増やしたいときは PostForm だけに手を入れればよいというように、変更の影響範囲を予測しやすくなるためである。