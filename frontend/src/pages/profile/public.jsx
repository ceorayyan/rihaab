import React, { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import Layout from '../../Components/Layout';

const PublicProfile = () => {
  const { username } = useParams();
  const [activeTab, setActiveTab] = useState('posts');

  // Mock user data - replace with actual API data
  const [user] = useState({
    id: 1,
    username: 'johndoe',
    name: 'John Doe',
    profile_picture: null,
    bio: 'Passionate about technology and creativity',
    dob: '1990-05-15',
    marital_status: 'Single',
    education: "Bachelor's in Computer Science",
    occupation: 'Software Engineer',
    stories: [
      {
        id: 1,
        type: 'image',
        content: 'path/to/story.jpg',
        caption: 'My day',
      },
    ],
    posts: [
      {
        id: 1,
        media_type: 'image',
        media_path: 'path/to/post.jpg',
        caption: 'Beautiful view!',
        created_at: new Date(Date.now() - 2 * 60 * 60 * 1000),
        likes_count: 45,
        comments_count: 12,
      },
      {
        id: 2,
        media_type: 'video',
        media_path: 'path/to/video.mp4',
        caption: 'Adventure time',
        created_at: new Date(Date.now() - 5 * 60 * 60 * 1000),
        likes_count: 78,
        comments_count: 23,
      },
      {
        id: 3,
        media_type: 'text',
        content: 'Just sharing some thoughts about life and technology...',
        created_at: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000),
        likes_count: 34,
        comments_count: 8,
      },
    ],
  });

  const postsCount = user.posts.length;
  const storiesCount = user.stories.length;

  const mediaPosts = user.posts.filter((p) => p.media_type === 'image' || p.media_type === 'video');
  const threadPosts = user.posts.filter((p) => p.media_type !== 'image' && p.media_type !== 'video');

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
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
        @keyframes slideIn {
          from { opacity: 0; transform: translateX(-20px); }
          to { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
          from { opacity: 0; transform: scale(0.95); }
          to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
          animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
          animation: slideIn 0.5s ease-out;
        }
        .animate-scale-in {
          animation: scaleIn 0.5s ease-out;
        }
        .grid-item {
          animation: fadeIn 0.4s ease-out forwards;
          opacity: 0;
        }
        .grid-item:nth-child(1) { animation-delay: 0.1s; }
        .grid-item:nth-child(2) { animation-delay: 0.15s; }
        .grid-item:nth-child(3) { animation-delay: 0.2s; }
        .grid-item:nth-child(4) { animation-delay: 0.25s; }
        .grid-item:nth-child(5) { animation-delay: 0.3s; }
        .grid-item:nth-child(6) { animation-delay: 0.35s; }
        .grid-item:nth-child(n+7) { animation-delay: 0.4s; }
        .no-scrollbar {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
        .no-scrollbar::-webkit-scrollbar {
          display: none;
        }
      `}</style>

      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Profile Header */}
        <section className="flex flex-col md:flex-row md:items-start md:space-x-12 animate-fade-in">
          {/* Avatar */}
          <div className="flex justify-center md:block md:shrink-0">
            <div
              className="relative w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl"
              style={{ ring: '3px solid #B6B09F' }}
            >
              {user.profile_picture ? (
                <img src={`/storage/${user.profile_picture}`} alt={user.name} className="w-full h-full object-cover" />
              ) : (
                <div className="w-full h-full flex items-center justify-center text-white" style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)' }}>
                  <span className="text-2xl font-semibold">{user.name.substring(0, 2).toUpperCase()}</span>
                </div>
              )}
            </div>
          </div>

          {/* Profile Meta */}
          <div className="flex-1 mt-4 md:mt-0 animate-slide-in">
            {/* Username + Actions */}
            <div className="flex items-center gap-2 sm:gap-4 flex-wrap">
              <h1 className="text-xl sm:text-2xl font-semibold" style={{ color: '#000000' }}>
                {user.username}
              </h1>
              <button
                className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
              >
                Send Request
              </button>
            </div>

            {/* Stats */}
            <ul className="mt-4 flex items-center gap-6 text-sm" style={{ color: '#000000' }}>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">{postsCount}</span> posts
              </li>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">{storiesCount}</span> stories
              </li>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">0</span> followers
              </li>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">0</span> following
              </li>
            </ul>

            {/* Bio & Extra Info */}
            <div className="mt-4 space-y-1 text-sm leading-relaxed">
              <p className="font-semibold" style={{ color: '#000000' }}>
                {user.name}
              </p>
              {user.bio && <p style={{ color: '#000000' }}>{user.bio}</p>}
              {user.dob && (
                <p style={{ color: '#000000' }}>
                  <span className="font-semibold">DOB:</span> {formatDate(user.dob)}
                </p>
              )}
              {user.marital_status && (
                <p style={{ color: '#000000' }}>
                  <span className="font-semibold">Marital Status:</span> {user.marital_status}
                </p>
              )}
              {user.education && (
                <p style={{ color: '#000000' }}>
                  <span className="font-semibold">Education:</span> {user.education}
                </p>
              )}
              {user.occupation && (
                <p style={{ color: '#000000' }}>
                  <span className="font-semibold">Occupation:</span> {user.occupation}
                </p>
              )}
            </div>
          </div>
        </section>

        {/* Stories Row */}
        {user.stories.length > 0 && (
          <div className="flex space-x-6 overflow-x-auto py-6 mt-6 no-scrollbar animate-slide-in" style={{ borderTop: '2px solid #B6B09F' }}>
            {user.stories.map((story) => (
              <div key={story.id} className="flex flex-col items-center transition-all duration-300 hover:scale-110 cursor-pointer">
                <div className="w-16 h-16 rounded-full overflow-hidden" style={{ border: '2px solid #000000' }}>
                  {story.type === 'image' ? (
                    <img src={`/storage/${story.content}`} className="w-full h-full object-cover" alt="Story" />
                  ) : (
                    <video className="w-full h-full object-cover">
                      <source src={`/storage/${story.content}`} type="video/mp4" />
                    </video>
                  )}
                </div>
                <p className="text-xs mt-1 w-16 text-center truncate" style={{ color: '#B6B09F' }}>
                  {story.caption || 'Story'}
                </p>
              </div>
            ))}
          </div>
        )}

        {/* Tabs */}
        <div>
          <nav className="mt-6" style={{ borderTop: '2px solid #B6B09F' }}>
            <ul className="flex items-center justify-center gap-10 text-xs tracking-widest uppercase font-semibold">
              <li>
                <button
                  onClick={() => setActiveTab('posts')}
                  className={`flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105 ${
                    activeTab === 'posts' ? 'border-t-2 -mt-[1px]' : ''
                  }`}
                  style={activeTab === 'posts' ? { borderColor: '#000000', color: '#000000' } : { color: '#B6B09F' }}
                >
                  Posts
                </button>
              </li>
              <li>
                <button
                  onClick={() => setActiveTab('threads')}
                  className={`flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105 ${
                    activeTab === 'threads' ? 'border-t-2 -mt-[1px]' : ''
                  }`}
                  style={activeTab === 'threads' ? { borderColor: '#000000', color: '#000000' } : { color: '#B6B09F' }}
                >
                  Threads
                </button>
              </li>
            </ul>
          </nav>

          {/* Posts Section */}
          {activeTab === 'posts' && (
            <section className="mt-2">
              {mediaPosts.length > 0 ? (
                <div className="grid grid-cols-3 gap-1 sm:gap-2">
                  {mediaPosts.map((post) => (
                    <Link key={post.id} to={`/profile/${user.username}/feed?postId=${post.id}`}>
                      <article
                        className="grid-item group relative aspect-square rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl"
                        style={{ backgroundColor: '#EAE4D5', border: '1px solid #B6B09F' }}
                      >
                        {post.media_type === 'image' ? (
                          <img src={`/storage/${post.media_path}`} alt="Post media" className="w-full h-full object-cover" />
                        ) : (
                          <>
                            <video className="w-full h-full object-cover">
                              <source src={`/storage/${post.media_path}`} type="video/mp4" />
                            </video>
                            <div className="absolute top-2 right-2" style={{ color: '#F2F2F2' }}>
                              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                              </svg>
                            </div>
                          </>
                        )}

                        {/* Hover Overlay */}
                        <div
                          className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
                          style={{ backgroundColor: 'rgba(0, 0, 0, 0.7)' }}
                        >
                          <div className="flex items-center space-x-4" style={{ color: '#F2F2F2' }}>
                            <div className="flex items-center">
                              <svg className="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                              </svg>
                              <span>{post.likes_count || 0}</span>
                            </div>
                            <div className="flex items-center">
                              <svg className="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 6h-2l-1.27-1.27A2 2 0 0 0 16.32 4H15V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h8.32a2 2 0 0 0 1.41-.59L21 15.93A2 2 0 0 0 21 14V8a2 2 0 0 0-2-2z" />
                              </svg>
                              <span>{post.comments_count || 0}</span>
                            </div>
                          </div>
                        </div>
                      </article>
                    </Link>
                  ))}
                </div>
              ) : (
                <div className="text-center py-12 animate-fade-in">
                  <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4h7v7H4V4zm0 9h7v7H4v-7zm9-9h7v7h-7V4zm0 9h7v7h-7v-7z" />
                  </svg>
                  <h3 className="text-2xl font-semibold mb-2" style={{ color: '#B6B09F' }}>
                    No Posts Yet
                  </h3>
                  <p style={{ color: '#B6B09F' }}>When {user.name} shares photos and videos, you'll see them here.</p>
                </div>
              )}
            </section>
          )}

          {/* Threads Section */}
          {activeTab === 'threads' && (
            <section className="mt-2">
              {threadPosts.length > 0 ? (
                <div className="space-y-4">
                  {threadPosts.map((post) => (
                    <div
                      key={post.id}
                      className="p-4 rounded-lg shadow-sm animate-fade-in transition-all duration-300 hover:shadow-lg"
                      style={{ backgroundColor: '#EAE4D5', border: '1px solid #B6B09F' }}
                    >
                      <div className="flex items-start space-x-3">
                        <div className="w-10 h-10 rounded-full overflow-hidden shrink-0">
                          {user.profile_picture ? (
                            <img src={`/storage/${user.profile_picture}`} className="w-full h-full object-cover" alt={user.name} />
                          ) : (
                            <div className="flex items-center justify-center w-full h-full text-sm font-bold" style={{ backgroundColor: '#B6B09F', color: '#F2F2F2' }}>
                              {user.name.charAt(0).toUpperCase()}
                            </div>
                          )}
                        </div>
                        <div className="flex-1">
                          <div className="flex items-center space-x-2 mb-2">
                            <h4 className="font-semibold" style={{ color: '#000000' }}>
                              {user.username}
                            </h4>
                            <span className="text-sm" style={{ color: '#B6B09F' }}>
                              {getTimeAgo(post.created_at)}
                            </span>
                          </div>
                          <p className="whitespace-pre-line" style={{ color: '#000000' }}>
                            {post.caption || post.content}
                          </p>
                          <div className="flex items-center space-x-4 mt-3 text-sm" style={{ color: '#B6B09F' }}>
                            <button className="flex items-center space-x-1 transition-colors duration-300 hover:text-red-500">
                              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                              </svg>
                              <span>{post.likes_count || 0}</span>
                            </button>
                            <button className="flex items-center space-x-1 transition-colors duration-300 hover:text-blue-500">
                              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 6h-2l-1.27-1.27A2 2 0 0 0 16.32 4H15V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h8.32a2 2 0 0 0 1.41-.59L21 15.93A2 2 0 0 0 21 14V8a2 2 0 0 0-2-2z" />
                              </svg>
                              <span>{post.comments_count || 0}</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-12 animate-fade-in">
                  <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <h3 className="text-2xl font-semibold mb-2" style={{ color: '#B6B09F' }}>
                    No Threads Yet
                  </h3>
                  <p style={{ color: '#B6B09F' }}>When {user.name} shares thoughts and updates, you'll see them here.</p>
                </div>
              )}
            </section>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default PublicProfile;