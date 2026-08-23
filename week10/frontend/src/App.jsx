import { useEffect, useState } from 'react';
import axios from 'axios';
import { apiClient } from './api/client';
import PostList from './components/PostList';
import PostForm from './components/PostForm';
import LoginForm from './components/LoginForm';

function App() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [user, setUser] = useState(null);
  const [authChecked, setAuthChecked] = useState(false);
  const [loginError, setLoginError] = useState(null);

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

  const fetchUser = async () => {
    try {
      const res = await apiClient.get('/user');
      setUser(res.data.user);
    } catch {
      setUser(null);
    } finally {
      setAuthChecked(true);
    }
  };

  const handleLogin = async ({ email, password }) => {
    try {
      setLoginError(null);
      // SanctumはログインAPIの前にCSRF Cookieの取得が必要
      await axios.get('http://localhost:8090/sanctum/csrf-cookie', { withCredentials: true });
      await apiClient.post('/login', { email, password });
      await fetchUser();
    } catch (err) {
      setLoginError('メールアドレスまたはパスワードが正しくありません');
      console.error(err);
    }
  };

  const handleLogout = async () => {
    await apiClient.post('/logout');
    setUser(null);
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
    fetchUser();
    fetchPosts();
  }, []);

  return (
    <div style={{ maxWidth: '600px', margin: '40px auto', fontFamily: 'sans-serif' }}>
      <h1>投稿一覧</h1>

      {authChecked && !user && (
        <LoginForm onLogin={handleLogin} error={loginError} />
      )}

      {authChecked && user && (
        <div style={{ marginBottom: '16px' }}>
          <span>ログイン中: {user.name}</span>
          <button onClick={handleLogout} style={{ marginLeft: '8px' }}>ログアウト</button>
        </div>
      )}

      {user && <PostForm onCreated={handleCreated} />}

      {loading && <p>読み込み中...</p>}
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {!loading && !error && <PostList posts={posts} />}
    </div>
  );
}

export default App;