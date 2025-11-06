import React, { useState, useEffect } from 'react';
import Layout from '../../Components/Layout';

const Feed = () => {
  // Mock posts data - replace with actual API data
  const [posts] = useState([
    {
      id: 1,
      user: {
        id: 1,
        name: 'John Doe',
        profile_picture: null,
      },
      media_type: 'image',
      media_path: 'path/to/image.jpg',
      content: 'Check out this amazing view!',
      created_at: new Date(Date.now() - 2 * 60 * 60 * 1000),
      likes_count: 45,
      comments_count: 12,
      comments: [
        { id: 1, user: { name: 'Jane Smith' }, content: 'Amazing!' },
        { id: 2, user: { name: 'Bob Johnson' }, content: 'Love it!' },
      ],
    },
    {
      id: 2,
      user: {
        id: 2,
        name: 'Jane Smith',
        profile_picture: null,
      },
      media_type: 'video',
      media_path: 'path/to/video.mp4',
      content: 'My latest adventure',
      created_at: new Date(Date.now() - 5 * 60 * 60 * 1000),
      likes_count: 78,
      comments_count: 23,
      comments: [],
    },
  ]);

  const [commentsVisible, setCommentsVisible] = useState({});

  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const startId = urlParams.get('startId');
    if (startId) {
      const element = document.getElementById(`post-${startId}`);
      if (element) {
        element.scrollIntoView({ behavior: 'instant', block: 'start' });
      }
    }
  }, []);

  const toggleComments = (postId) => {
    setCommentsVisible((prev) => ({
      ...prev,
      [postId]: !prev[postId],
    }));
  };

  const likePost = async (postId) => {
    console.log('Liking post:', postId);
    // Handle like logic here
  };

  const handleCommentSubmit = (e, postId) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const content = formData.get('content');
    console.log('Submitting comment:', { postId, content });
    e.target.reset();
  };

  return (
    <Layout>
      <style>{`
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      `}</style>

      <div className="max-w-4xl mx-auto">
        <div className="snap-y snap-mandatory overflow-y-scroll h-screen no-scrollbar">
          {posts
            .filter((post) => post.media_type === 'image' || post.media_type === 'video')
            .map((post) => (
              <div key={post.id} id={`post-${post.id}`} className="h-screen flex items-center justify-center snap-start">
                <article className="bg-white rounded-xl shadow-sm border border-gray-100 w-full max-w-lg mx-auto">
                  {/* Post Header */}
                  <div className="p-4 flex items-center gap-3 border-b">
                    <div className="w-10 h-10 rounded-full overflow-hidden">
                      {post.user.profile_picture ? (
                        <img src={`/storage/${post.user.profile_picture}`} alt={post.user.name} className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gray-300 text-white font-bold">
                          {post.user.name.charAt(0).toUpperCase()}
                        </div>
                      )}
                    </div>
                    <div>
                      <p className="font-semibold">{post.user.name}</p>
                      <p className="text-xs text-gray-500">{new Date(post.created_at).toLocaleString()}</p>
                    </div>
                  </div>

                  {/* Media */}
                  <div className="bg-black flex items-center justify-center max-h-[70vh]">
                    {post.media_type === 'image' ? (
                      <img src={`/storage/${post.media_path}`} className="max-h-[70vh] object-contain" alt="Post content" />
                    ) : (
                      <video controls autoPlay loop muted className="max-h-[70vh] w-full object-contain">
                        <source src={`/storage/${post.media_path}`} type="video/mp4" />
                      </video>
                    )}
                  </div>

                  {/* Caption */}
                  {post.content && (
                    <div className="px-4 py-3">
                      <p className="text-gray-800 text-sm">{post.content}</p>
                    </div>
                  )}

                  {/* Post Actions */}
                  <div className="px-4 py-2 border-t flex items-center gap-6">
                    <button onClick={() => likePost(post.id)} className="flex items-center gap-2 text-gray-600 hover:text-red-500">
                      ❤️ <span id={`like-count-${post.id}`}>{post.likes_count}</span>
                    </button>
                    <button onClick={() => toggleComments(post.id)} className="flex items-center gap-2 text-gray-600 hover:text-blue-500">
                      💬 <span>{post.comments_count}</span>
                    </button>
                  </div>

                  {/* Comments Section */}
                  {commentsVisible[post.id] && (
                    <div className="border-t px-4 py-3 space-y-3">
                      {/* Add Comment */}
                      <form className="flex gap-2" onSubmit={(e) => handleCommentSubmit(e, post.id)}>
                        <input
                          type="text"
                          name="content"
                          placeholder="Write a comment..."
                          className="flex-1 border rounded-full px-3 py-1 text-sm"
                          required
                        />
                      </form>

                      {/* Comments List */}
                      <div className="space-y-2">
                        {post.comments.map((comment) => (
                          <div key={comment.id} className="flex gap-2">
                            <strong>{comment.user.name}</strong>
                            <span className="text-gray-700 text-sm">{comment.content}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </article>
              </div>
            ))}
        </div>
      </div>
    </Layout>
  );
};

export default Feed;