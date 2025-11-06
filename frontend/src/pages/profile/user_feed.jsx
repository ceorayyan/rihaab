import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import Layout from '../../Components/Layout';

const UserFeed = () => {
  const navigate = useNavigate();
  const { username } = useParams();

  // Mock user data - replace with actual API data
  const [user] = useState({
    id: 1,
    username: 'johndoe',
    name: 'John Doe',
    profile_picture: null,
  });

  // Mock posts data
  const [mediaPosts] = useState([
    {
      id: 1,
      media_type: 'image',
      media_path: 'path/to/image.jpg',
      caption: 'Beautiful sunset view!',
      created_at: new Date(Date.now() - 2 * 60 * 60 * 1000),
      likes_count: 45,
      comments_count: 12,
    },
    {
      id: 2,
      media_type: 'video',
      media_path: 'path/to/video.mp4',
      caption: 'Adventure time!',
      created_at: new Date(Date.now() - 5 * 60 * 60 * 1000),
      likes_count: 78,
      comments_count: 23,
    },
  ]);

  const [likedPosts, setLikedPosts] = useState({});
  const [savedPosts, setSavedPosts] = useState({});

  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const scrollToIndex = urlParams.get('scrollToIndex');
    if (scrollToIndex !== null) {
      setTimeout(() => {
        const targetPost = document.querySelector(`[data-post-index="${scrollToIndex}"]`);
        if (targetPost) {
          targetPost.scrollIntoView({ behavior: 'smooth', block: 'start' });
          targetPost.style.boxShadow = '0 0 0 3px #000000';
          setTimeout(() => {
            targetPost.style.boxShadow = '';
          }, 2000);
        }
      }, 100);
    }
  }, []);

  const handleLike = (postId) => {
    setLikedPosts((prev) => ({
      ...prev,
      [postId]: !prev[postId],
    }));
  };

  const handleSave = (postId) => {
    setSavedPosts((prev) => ({
      ...prev,
      [postId]: !prev[postId],
    }));
  };

  const getTimeAgo = (date) => {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    const intervals = {
      year: 31536000,
      month: 2592000,
      week: 604800,
      day: 86400,
      hour: 3600,
      minute: 60,
    };

    for (const [key, value] of Object.entries(intervals)) {
      const interval = Math.floor(seconds / value);
      if (interval >= 1) {
        return `${interval} ${key}${interval > 1 ? 's' : ''} ago`;
      }
    }
    return 'just now';
  };

  return (
    <Layout>
      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(10px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
          animation: fadeIn 0.6s ease-out;
        }
      `}</style>

      <div className="max-w-lg mx-auto min-h-screen" style={{ backgroundColor: '#F2F2F2' }}>
        {/* Header */}
        <div className="sticky top-0 z-10 px-4 py-3 animate-fade-in" style={{ backgroundColor: '#F2F2F2', borderBottom: '2px solid #B6B09F' }}>
          <div className="flex items-center justify-between">
            <button onClick={() => navigate(`/profile/${username}`)} style={{ color: '#000000' }}>
              <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
              </svg>
            </button>
            <h1 className="font-semibold text-lg" style={{ color: '#000000' }}>
              {user.username}
            </h1>
            <div className="w-6"></div>
          </div>
        </div>

        {/* Feed Posts */}
        <div className="pb-4">
          {mediaPosts.length > 0 ? (
            mediaPosts.map((post, index) => (
              <div
                key={post.id}
                id={`post-${post.id}`}
                data-post-index={index}
                className="mb-6 animate-fade-in rounded-lg overflow-hidden"
                style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}
              >
                {/* Post Header */}
                <div className="flex items-center px-4 py-3">
                  <div className="w-8 h-8 rounded-full overflow-hidden mr-3">
                    {user.profile_picture ? (
                      <img src={`/storage/${user.profile_picture}`} className="w-full h-full object-cover" alt={user.name} />
                    ) : (
                      <div
                        className="flex items-center justify-center w-full h-full text-xs font-bold"
                        style={{ backgroundColor: '#B6B09F', color: '#F2F2F2' }}
                      >
                        {user.name.charAt(0).toUpperCase()}
                      </div>
                    )}
                  </div>
                  <div className="flex-1">
                    <h4 className="font-semibold text-sm" style={{ color: '#000000' }}>
                      {user.username}
                    </h4>
                  </div>
                  <button style={{ color: '#B6B09F' }}>
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                    </svg>
                  </button>
                </div>

                {/* Post Media */}
                <div className="relative" style={{ backgroundColor: '#000000' }}>
                  {post.media_type === 'image' ? (
                    <img src={`/storage/${post.media_path}`} className="w-full h-auto max-h-96 object-contain" alt="Post content" />
                  ) : (
                    <video controls className="w-full h-auto max-h-96 object-contain">
                      <source src={`/storage/${post.media_path}`} type="video/mp4" />
                      Your browser does not support the video tag.
                    </video>
                  )}
                </div>

                {/* Post Actions */}
                <div className="px-4 py-3">
                  <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center space-x-4">
                      <button
                        onClick={() => handleLike(post.id)}
                        className="transition-colors duration-300"
                        style={{ color: likedPosts[post.id] ? '#EF4444' : '#000000' }}
                      >
                        <svg
                          className="w-6 h-6"
                          fill={likedPosts[post.id] ? 'currentColor' : 'none'}
                          stroke={likedPosts[post.id] ? 'none' : 'currentColor'}
                          strokeWidth="2"
                          viewBox="0 0 24 24"
                        >
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                          />
                        </svg>
                      </button>
                      <button className="transition-colors duration-300 hover:text-blue-500" style={{ color: '#000000' }}>
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                          />
                        </svg>
                      </button>
                      <button className="transition-colors duration-300 hover:text-green-500" style={{ color: '#000000' }}>
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"
                          />
                        </svg>
                      </button>
                    </div>
                    <button
                      onClick={() => handleSave(post.id)}
                      className="transition-colors duration-300"
                      style={{ color: savedPosts[post.id] ? '#EAB308' : '#000000' }}
                    >
                      <svg
                        className="w-6 h-6"
                        fill={savedPosts[post.id] ? 'currentColor' : 'none'}
                        stroke={savedPosts[post.id] ? 'none' : 'currentColor'}
                        strokeWidth="2"
                        viewBox="0 0 24 24"
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                      </svg>
                    </button>
                  </div>

                  {/* Likes Count */}
                  <div className="mb-2">
                    <p className="font-semibold text-sm" style={{ color: '#000000' }}>
                      {likedPosts[post.id] ? (post.likes_count || 0) + 1 : post.likes_count || 0} likes
                    </p>
                  </div>

                  {/* Caption */}
                  {post.caption && (
                    <div className="mb-2">
                      <p className="text-sm" style={{ color: '#000000' }}>
                        <span className="font-semibold">{user.username}</span> {post.caption}
                      </p>
                    </div>
                  )}

                  {/* Comments Link */}
                  {post.comments_count > 0 && (
                    <button className="text-sm mb-2 transition-colors duration-300" style={{ color: '#B6B09F' }}>
                      View all {post.comments_count} comments
                    </button>
                  )}

                  {/* Post Date */}
                  <p className="text-xs uppercase tracking-wide" style={{ color: '#B6B09F' }}>
                    {getTimeAgo(post.created_at)}
                  </p>
                </div>
              </div>
            ))
          ) : (
            /* No posts message */
            <div className="text-center py-12 animate-fade-in">
              <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 24 24">
                <path d="M4 4h7v7H4V4zm0 9h7v7H4v-7zm9-9h7v7h-7V4zm0 9h7v7h-7v-7z" />
              </svg>
              <h3 className="text-xl font-semibold mb-2" style={{ color: '#B6B09F' }}>
                No Posts Yet
              </h3>
              <p style={{ color: '#B6B09F' }}>When {user.name} shares photos and videos, they'll appear here.</p>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default UserFeed;