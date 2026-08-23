import { useState } from 'react';

export default function LoginForm({ onLogin, error }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    await onLogin({ email, password });
    setSubmitting(false);
  };

  return (
    <form onSubmit={handleSubmit} style={{ marginBottom: '24px', border: '1px solid #ddd', padding: '16px', borderRadius: '8px' }}>
      <h2 style={{ marginTop: 0 }}>ログイン</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <div style={{ marginBottom: '8px' }}>
        <input
          type="email"
          placeholder="メールアドレス"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          style={{ width: '100%', padding: '8px' }}
        />
      </div>
      <div style={{ marginBottom: '8px' }}>
        <input
          type="password"
          placeholder="パスワード"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          style={{ width: '100%', padding: '8px' }}
        />
      </div>
      <button type="submit" disabled={submitting}>
        {submitting ? 'ログイン中...' : 'ログイン'}
      </button>
    </form>
  );
}