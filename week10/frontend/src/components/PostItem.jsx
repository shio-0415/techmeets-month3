export default function PostItem({ post }) {
  return (
    <li style={{ marginBottom: '16px', borderBottom: '1px solid #ddd', paddingBottom: '8px' }}>
      <h3>{post.title}</h3>
      <p>{post.content}</p>
      <small>投稿者: {post.user.name} / {post.created_at}</small>
    </li>
  );
}