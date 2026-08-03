import { useEffect, useState } from 'react';
import { apiClient } from './api/client';
import PostList from './components/PostList';
import PostForm from './components/PostForm';

function App() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchPosts = async () => {
    try {
      setLoading(true);
      const res = await apiClient.get('/posts');
      setPosts(res.data.data);
      setError(null);
    } catch (err) {
      setError('データの取得に失敗しました');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleCreated = async (formData) => {
    try {
      await apiClient.post('/posts', formData);
      await fetchPosts(); // 保存後に一覧を再取得
    } catch (err) {
      setError('投稿の作成に失敗しました');
      console.error(err);
    }
  };

  useEffect(() => {
    fetchPosts();
  }, []);

  return (
    <div style={{ maxWidth: '600px', margin: '40px auto', fontFamily: 'sans-serif' }}>
      <h1>投稿一覧</h1>
      <PostForm onCreated={handleCreated} />
      {loading && <p>読み込み中...</p>}
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {!loading && !error && <PostList posts={posts} />}
    </div>
  );
}

export default App;