# Week9 基本課題: Repository/Serviceパターンでリファクタリング

Week7・8で作成したブログアプリ（投稿のCRUD機能）を、Repository/Serviceパターンに書き直しました。

## 設計
- `app/Repositories/Contracts/PostRepositoryInterface.php` … Repositoryの取り決め（インターフェース）
- `app/Repositories/PostRepository.php` … 実際のDB操作
- `app/Services/PostService.php` … 業務ロジック（投稿者IDの付与など）
- `app/Http/Requests/StorePostRequest.php` / `UpdatePostRequest.php` … バリデーションの分離
- `app/Policies/PostPolicy.php` … 認可（投稿の所有者のみ編集・削除可能）
- `app/Providers/AppServiceProvider.php` … インターフェースと実装クラスの依存性注入（DI）バインディング

## Before / After

### Before（Week8時点）
`PostController` の中に、バリデーション・DB操作・認可チェックがすべて直接書かれていた。

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);
    $validated['user_id'] = auth()->id();
    Post::create($validated);
    return redirect()->route('posts.index')->with('success', '投稿しました');
}
```

コントローラーが「バリデーション」「DB操作」を両方担当しており、テストしづらく、再利用もしにくい状態だった。

### After（Week9でリファクタリング後）

```php
public function store(StorePostRequest $request)
{
    $this->postService->createPost($request->validated(), auth()->id());
    return redirect()->route('posts.index')->with('success', '投稿しました');
}
```

- バリデーションは `StorePostRequest` に分離
- DB操作は `PostService` → `PostRepository` に分離
- コントローラーは「リクエストを受け取り、Serviceに渡し、レスポンスを返す」だけのシンプルな役割になった
- `PostRepositoryInterface` を介して依存しているため、将来DBの実装を変えたり、テスト時にモック（偽のRepository）に差し替えることが容易になった

## N+1問題対策

投稿一覧表示時、`Post::with('user')->latest()->paginate()` のように `with()` を使い、投稿者情報を事前読み込みすることで、N+1問題（投稿の数だけ追加クエリが発生する問題）を回避している。

## 起動方法

```bash
docker compose up -d
docker compose exec app php artisan migrate
npm install
npm run build
```

http://localhost:8090/register にアクセス
