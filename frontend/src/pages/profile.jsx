import React, { useState, useEffect } from 'react';
import Layout from '../components/Layout';
import { apiCall, getMediaUrl, formatDate, formatTimeAgo } from '../config';

const Profile = ({ onNavigate }) => {
  const [activeTab, setActiveTab] = useState('posts');
  const [user, setUser] = useState(null);
  const [mediaPosts, setMediaPosts] = useState([]);
  const [threadPosts, setThreadPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProfileData();
  }, []);

  const fetchProfileData = async () => {
    try {
      setLoading(true);
      setError(null);

      console.log('🔄 Fetching profile data...');

      const data = await apiCall('/profile');
      console.log('📦 Profile data:', data);

      if (data.success) {
        setUser(data.data.user);
        
        // Separate media and thread posts
        const posts = data.data.posts || [];
        console.log('📝 Total posts:', posts.length);
        
        const media = posts.filter(post => ['image', 'video'].includes(post.media_type));
        const threads = posts.filter(post => !['image', 'video'].includes(post.media_type));
        
        console.log('🖼️ Media posts:', media.length);
        console.log('💬 Thread posts:', threads.length);
        
        setMediaPosts(media);
        setThreadPosts(threads);
      }
    } catch (err) {
      console.error('❌ Error fetching profile:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <Layout>
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2" style={{ borderColor: '#000000' }}></div>
          </div>
        </div>
      </Layout>
    );
  }

  if (error) {
    return (
      <Layout>
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
          <div className="text-center py-12">
            <p className="text-red-600 mb-4">{error}</p>
            <div className="flex gap-4 justify-center">
              <button 
                onClick={fetchProfileData}
                className="px-6 py-2 rounded-lg transition-all duration-300 hover:scale-105"
                style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
              >
                Retry
              </button>
              {error.includes('Session expired') && (
                <button 
                  onClick={() => {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                  }}
                  className="px-6 py-2 rounded-lg transition-all duration-300 hover:scale-105"
                  style={{ backgroundColor: '#B6B09F', color: '#000000' }}
                >
                  Go to Login
                </button>
              )}
            </div>
          </div>
        </div>
      </Layout>
    );
  }

  if (!user) return null;

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
        .animate-fade-in {
          animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
          animation: slideIn 0.5s ease-out;
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
      `}</style>

      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Top Section */}
        <section className="flex flex-col md:flex-row md:items-start md:space-x-12 animate-fade-in">
          {/* Avatar */}
          <div className="flex justify-center md:block md:shrink-0">
            <div 
              className="relative w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl" 
              style={{ border: '3px solid #B6B09F' }}
            >
              {user.profile_picture ? (
                <img src={getMediaUrl(user.profile_picture)} alt={user.name} className="w-full h-full object-cover" />
              ) : (
                <div 
                  className="w-full h-full flex items-center justify-center text-white" 
                  style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)' }}
                >
                  <span className="text-2xl font-semibold">{user.name.substring(0, 2).toUpperCase()}</span>
                </div>
              )}
            </div>
          </div>

          {/* Profile Meta */}
          <div className="flex-1 mt-4 md:mt-0 animate-slide-in">
            {/* Username + Actions */}
            <div className="flex items-center gap-2 sm:gap-4 flex-wrap">
              <h1 className="text-xl sm:text-2xl font-semibold" style={{ color: '#000000' }}>{user.name}</h1>
              <button 
                onClick={() => onNavigate && onNavigate('edit-profile')}
                className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg" 
                style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '2px solid #B6B09F' }}
              >
                Edit Profile
              </button>
            </div>

            {/* Stats */}
            <ul className="mt-4 flex items-center gap-6 text-sm" style={{ color: '#000000' }}>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">{mediaPosts.length + threadPosts.length}</span> posts
              </li>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">{user.key_friends_count || 0}</span> key friends
              </li>
              <li className="transition-all duration-300 hover:scale-110">
                <span className="font-semibold">{user.pending_key_requests_count || 0}</span> pending requests
              </li>
            </ul>

            {/* Bio & Extra Info */}
            <div className="mt-4 space-y-1 text-sm leading-relaxed">
              <p className="font-semibold" style={{ color: '#000000' }}>{user.name}</p>
              {user.username && <p style={{ color: '#B6B09F' }}>@{user.username}</p>}
              <p style={{ color: '#B6B09F' }}>{user.email}</p>
              {user.bio && <p style={{ color: '#000000' }}>{user.bio}</p>}
              {user.education && <p style={{ color: '#000000' }}><span className="font-semibold">Education:</span> {user.education}</p>}
              {user.occupation && <p style={{ color: '#000000' }}><span className="font-semibold">Occupation:</span> {user.occupation}</p>}
              {user.marital_status && <p style={{ color: '#000000' }}><span className="font-semibold">Marital Status:</span> {user.marital_status}</p>}
              {user.dob && <p style={{ color: '#000000' }}><span className="font-semibold">DOB:</span> {formatDate(user.dob)}</p>}
            </div>
          </div>
        </section>

        {/* Tabs */}
        <div>
          <nav className="mt-6" style={{ borderTop: '2px solid #B6B09F' }}>
            <ul className="flex items-center justify-center gap-10 text-xs tracking-widest uppercase font-semibold">
              <li>
                <button 
                  onClick={() => setActiveTab('posts')}
                  className={`flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105 ${activeTab === 'posts' ? 'border-t-2 -mt-[1px]' : ''}`}
                  style={activeTab === 'posts' ? { borderColor: '#000000', color: '#000000' } : { color: '#B6B09F' }}
                >
                  Posts
                </button>
              </li>
              <li>
                <button 
                  onClick={() => setActiveTab('threads')}
                  className={`flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105 ${activeTab === 'threads' ? 'border-t-2 -mt-[1px]' : ''}`}
                  style={activeTab === 'threads' ? { borderColor: '#000000', color: '#000000' } : { color: '#B6B09F' }}
                >
                  Threads
                </button>
              </li>
            </ul>
          </nav>

          {/* Posts Section */}
          {activeTab === 'posts' && (
            <section className="mt-2" aria-label="Posts">
              {mediaPosts.length > 0 ? (
                <div className="grid grid-cols-3 gap-1 sm:gap-2">
                  {mediaPosts.map((post) => (
                    <article 
                      key={post.id}
                      onClick={() => onNavigate && onNavigate('feed', { postId: post.id })}
                      className="grid-item group relative aspect-square rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer" 
                      style={{ backgroundColor: '#EAE4D5', border: '1px solid #B6B09F' }}
                    >
                      <img 
                        src={getMediaUrl(post.media_path)} 
                        alt="Post media"
                        className="w-full h-full object-cover" 
                      />
                      <div 
                        className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center" 
                        style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }}
                      >
                        <div className="text-center">
                          <span className="font-semibold text-sm block" style={{ color: '#F2F2F2' }}>View</span>
                          {post.likes_count > 0 && (
                            <span className="text-xs" style={{ color: '#F2F2F2' }}>❤️ {post.likes_count}</span>
                          )}
                        </div>
                      </div>
                    </article>
                  ))}
                </div>
              ) : (
                <p className="text-center mt-8" style={{ color: '#B6B09F' }}>No media posts yet.</p>
              )}
            </section>
          )}

          {/* Threads Section */}
          {activeTab === 'threads' && (
            <section className="mt-2" aria-label="Threads">
              {threadPosts.length > 0 ? (
                <div className="space-y-4">
                  {threadPosts.map((post) => (
                    <div 
                      key={post.id}
                      className="p-4 rounded-lg shadow-sm animate-fade-in transition-all duration-300 hover:shadow-lg" 
                      style={{ backgroundColor: '#EAE4D5', border: '1px solid #B6B09F' }}
                    >
                      <p className="whitespace-pre-line" style={{ color: '#000000' }}>{post.content}</p>
                      <div className="flex items-center gap-4 mt-2 text-xs" style={{ color: '#B6B09F' }}>
                        <span>{formatTimeAgo(post.created_at)}</span>
                        {post.likes_count > 0 && <span>❤️ {post.likes_count}</span>}
                        {post.comments_count > 0 && <span>💬 {post.comments_count}</span>}
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-center mt-8" style={{ color: '#B6B09F' }}>No threads yet.</p>
              )}
            </section>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default Profile;