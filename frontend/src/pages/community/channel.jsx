import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import Layout from '../../Components/Layout';

const CommunityChannel = () => {
  const [showEditPostModal, setShowEditPostModal] = useState(false);
  const [showEditChannelModal, setShowEditChannelModal] = useState(false);
  const [showCreateChannelModal, setShowCreateChannelModal] = useState(false);
  const [editPostData, setEditPostData] = useState({ id: null, content: '' });
  const [postContent, setPostContent] = useState('');

  // Mock data
  const community = {
    slug: 'tech-enthusiasts',
    name: 'Tech Enthusiasts',
    channels: [
      { id: 1, slug: 'general', name: 'General', type: 'general', description: 'General discussions' },
      { id: 2, slug: 'announcements', name: 'Announcements', type: 'announcement' },
    ],
  };

  const channel = {
    id: 1,
    slug: 'general',
    name: 'General',
    description: 'General discussions about technology',
    type: 'general',
    is_private: false,
  };

  const posts = [
    {
      id: 1,
      content: 'Welcome to the community! Feel free to introduce yourself.',
      is_pinned: true,
      created_at: new Date(Date.now() - 2 * 60 * 60 * 1000),
      user: { id: 1, username: 'admin', name: 'Admin User', profile_picture: null },
      media_path: null,
      media_type: null,
      reactions: [],
    },
  ];

  const userRole = 'admin';
  const canPost = true;
  const isChannelModerator = false;

  const handlePostSubmit = (e) => {
    e.preventDefault();
    console.log('Creating post:', postContent);
    setPostContent('');
  };

  const openEditPostModal = (postId, content) => {
    setEditPostData({ id: postId, content });
    setShowEditPostModal(true);
  };

  const handleEditPostSubmit = (e) => {
    e.preventDefault();
    console.log('Updating post:', editPostData);
    setShowEditPostModal(false);
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
        .post-item {
          animation: fadeIn 0.4s ease-out forwards;
          opacity: 0;
        }
        .post-item:nth-child(1) { animation-delay: 0.1s; }
        .post-item:nth-child(2) { animation-delay: 0.15s; }
        .post-item:nth-child(3) { animation-delay: 0.2s; }
        .post-item:nth-child(n+4) { animation-delay: 0.25s; }
        .active-channel {
          background-color: #EAE4D5 !important;
          font-weight: 600;
        }
      `}</style>

      <div className="flex" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Channels Sidebar */}
        <div className="w-64 shrink-0" style={{ backgroundColor: '#FFFFFF', borderRight: '2px solid #B6B09F' }}>
          {/* Community Header */}
          <div className="p-4" style={{ borderBottom: '2px solid #B6B09F' }}>
            <Link to={`/communities/${community.slug}`} className="flex items-center space-x-3 mb-3">
              <div className="w-12 h-12 rounded-lg overflow-hidden" style={{ backgroundColor: '#EAE4D5' }}>
                <div className="w-full h-full flex items-center justify-center font-bold" style={{ color: '#000000' }}>
                  {community.name.substring(0, 2).toUpperCase()}
                </div>
              </div>
              <div className="flex-1 min-w-0">
                <h2 className="font-bold truncate" style={{ color: '#000000' }}>
                  {community.name}
                </h2>
              </div>
            </Link>
          </div>

          {/* Channels List */}
          <div className="p-4">
            <div className="flex items-center justify-between mb-3">
              <h3 className="text-xs font-semibold uppercase tracking-wide" style={{ color: '#B6B09F' }}>
                Channels
              </h3>
              {(userRole === 'admin' || userRole === 'moderator') && (
                <button onClick={() => setShowCreateChannelModal(true)} className="text-xs transition-colors duration-300 hover:text-black" style={{ color: '#B6B09F' }}>
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                  </svg>
                </button>
              )}
            </div>

            <div className="space-y-1">
              {community.channels.map((ch) => (
                <Link
                  key={ch.id}
                  to={`/communities/${community.slug}/channels/${ch.slug}`}
                  className={`flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 ${channel.id === ch.id ? 'active-channel' : ''}`}
                  style={{ color: '#000000' }}
                >
                  {ch.type === 'announcement' ? (
                    <svg className="w-4 h-4 shrink-0" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                  ) : (
                    <svg className="w-4 h-4 shrink-0" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clipRule="evenodd" />
                    </svg>
                  )}
                  <span className="text-sm truncate">{ch.name}</span>
                </Link>
              ))}
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="flex-1 flex flex-col max-h-screen">
          {/* Channel Header */}
          <div className="px-6 py-4 animate-fade-in" style={{ backgroundColor: '#FFFFFF', borderBottom: '2px solid #B6B09F' }}>
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-2xl font-bold flex items-center space-x-2" style={{ color: '#000000' }}>
                  {channel.type === 'announcement' && (
                    <svg className="w-6 h-6" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                  )}
                  <span>{channel.name}</span>
                  {channel.is_private && (
                    <svg className="w-5 h-5" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                    </svg>
                  )}
                </h1>
                {channel.description && (
                  <p className="text-sm mt-1" style={{ color: '#B6B09F' }}>
                    {channel.description}
                  </p>
                )}
              </div>

              <div className="flex items-center space-x-2">
                {userRole === 'admin' && (
                  <>
                    <Link
                      to={`/communities/${community.slug}/channels/${channel.slug}/moderators`}
                      className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                      style={{ backgroundColor: '#E0E7FF', color: '#3730A3', border: '1px solid #818CF8' }}
                    >
                      Manage Moderators
                    </Link>
                    <button
                      onClick={() => setShowEditChannelModal(true)}
                      className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                      style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '1px solid #B6B09F' }}
                    >
                      Edit Channel
                    </button>
                  </>
                )}
              </div>
            </div>
          </div>

          {/* Posts Area */}
          <div className="flex-1 overflow-y-auto p-6">
            <div className="max-w-3xl mx-auto space-y-4">
              {/* Create Post Form */}
              {canPost && (
                <div className="rounded-lg p-4 animate-fade-in" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                  <form onSubmit={handlePostSubmit}>
                    <div className="flex space-x-3">
                      <div className="w-10 h-10 rounded-full overflow-hidden shrink-0" style={{ backgroundColor: '#EAE4D5' }}>
                        <div className="w-full h-full flex items-center justify-center font-bold text-sm" style={{ color: '#000000' }}>
                          U
                        </div>
                      </div>

                      <div className="flex-1">
                        <textarea
                          name="content"
                          rows="3"
                          required
                          value={postContent}
                          onChange={(e) => setPostContent(e.target.value)}
                          placeholder="Share something with the community..."
                          className="w-full px-4 py-2 rounded-lg mb-2 resize-none"
                          style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                        />

                        <div className="flex items-center justify-between">
                          <label className="cursor-pointer text-sm flex items-center space-x-2 transition-colors duration-300 hover:text-black" style={{ color: '#B6B09F' }}>
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span>Attach file</span>
                            <input type="file" name="media" className="hidden" accept="image/*,video/*" />
                          </label>

                          <button
                            type="submit"
                            className="px-6 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                            style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                          >
                            Post
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              )}

              {!canPost && (
                <div className="rounded-lg p-4 text-center animate-fade-in" style={{ backgroundColor: '#EAE4D5', border: '1px solid #B6B09F' }}>
                  <p style={{ color: '#000000' }}>
                    {channel.type === 'announcement' ? 'Only admins can post in announcement channels.' : "You don't have permission to post in this channel."}
                  </p>
                </div>
              )}

              {/* Posts List */}
              {posts.length > 0 ? (
                posts.map((post) => (
                  <div key={post.id} className="post-item rounded-lg p-4" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                    <div className="flex items-start space-x-3">
                      {/* User Avatar */}
                      <Link to={`/profile/${post.user.username}`} className="shrink-0">
                        <div className="w-10 h-10 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" style={{ backgroundColor: '#EAE4D5' }}>
                          {post.user.profile_picture ? (
                            <img src={`/storage/${post.user.profile_picture}`} className="w-full h-full object-cover" alt={post.user.name} />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center font-bold text-sm" style={{ color: '#000000' }}>
                              {post.user.name.substring(0, 1).toUpperCase()}
                            </div>
                          )}
                        </div>
                      </Link>

                      <div className="flex-1 min-w-0">
                        {/* Post Header */}
                        <div className="flex items-center justify-between mb-2">
                          <div className="flex items-center space-x-2">
                            <Link to={`/profile/${post.user.username}`} className="font-semibold hover:underline" style={{ color: '#000000' }}>
                              {post.user.username}
                            </Link>
                            <span className="text-xs" style={{ color: '#B6B09F' }}>
                              {new Date(post.created_at).toLocaleString()}
                            </span>
                            {post.is_pinned && (
                              <span className="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style={{ backgroundColor: '#EAE4D5', color: '#000000' }}>
                                📌 Pinned
                              </span>
                            )}
                          </div>

                          {/* Post Actions Dropdown */}
                          <div className="relative">
                            <button style={{ color: '#B6B09F' }}>
                              <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                              </svg>
                            </button>
                          </div>
                        </div>

                        {/* Post Content */}
                        <p className="whitespace-pre-line mb-3" style={{ color: '#000000' }}>
                          {post.content}
                        </p>

                        {/* Post Media */}
                        {post.media_path && (
                          <div className="mb-3 rounded-lg overflow-hidden" style={{ border: '1px solid #B6B09F' }}>
                            {post.media_type === 'image' ? (
                              <img src={`/storage/${post.media_path}`} className="w-full" alt="Post media" />
                            ) : (
                              <video controls className="w-full">
                                <source src={`/storage/${post.media_path}`} type="video/mp4" />
                              </video>
                            )}
                          </div>
                        )}

                        {/* Post Reactions */}
                        <div className="flex items-center space-x-4 text-sm" style={{ color: '#B6B09F' }}>
                          <button className="flex items-center space-x-1 transition-colors duration-300 hover:text-red-500">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>{post.reactions.length}</span>
                          </button>
                          <button className="flex items-center space-x-1 transition-colors duration-300 hover:text-blue-500">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Reply</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-12 rounded-lg" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                  <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <h3 className="text-xl font-semibold mb-2" style={{ color: '#B6B09F' }}>
                    No Posts Yet
                  </h3>
                  <p style={{ color: '#B6B09F' }}>Be the first to start a conversation!</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Edit Post Modal */}
      {showEditPostModal && (
        <div className="fixed inset-0 z-50 overflow-y-auto" style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }}>
          <div className="flex items-center justify-center min-h-screen px-4">
            <div className="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style={{ backgroundColor: '#FFFFFF', border: '2px solid #B6B09F' }}>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-xl font-bold" style={{ color: '#000000' }}>
                  Edit Post
                </h3>
                <button onClick={() => setShowEditPostModal(false)} style={{ color: '#B6B09F' }}>
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <form onSubmit={handleEditPostSubmit}>
                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Content
                  </label>
                  <textarea
                    value={editPostData.content}
                    onChange={(e) => setEditPostData({ ...editPostData, content: e.target.value })}
                    rows="5"
                    required
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                  />
                </div>

                <div className="flex space-x-3">
                  <button
                    type="button"
                    onClick={() => setShowEditPostModal(false)}
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                    style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '1px solid #B6B09F' }}
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                    style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                  >
                    Update
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Edit Channel Modal */}
      {showEditChannelModal && userRole === 'admin' && (
        <div className="fixed inset-0 z-50 overflow-y-auto" style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }}>
          <div className="flex items-center justify-center min-h-screen px-4">
            <div className="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style={{ backgroundColor: '#FFFFFF', border: '2px solid #B6B09F' }}>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-xl font-bold" style={{ color: '#000000' }}>
                  Edit Channel
                </h3>
                <button onClick={() => setShowEditChannelModal(false)} style={{ color: '#B6B09F' }}>
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <form onSubmit={(e) => e.preventDefault()}>
                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Channel Name
                  </label>
                  <input
                    type="text"
                    defaultValue={channel.name}
                    required
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                  />
                </div>

                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Description
                  </label>
                  <textarea
                    defaultValue={channel.description}
                    rows="3"
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                  />
                </div>

                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Channel Type
                  </label>
                  <select defaultValue={channel.type} className="w-full px-4 py-2 rounded-lg" style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}>
                    <option value="general">General - All members can post</option>
                    <option value="announcement">Announcement - Only admins can post</option>
                    <option value="restricted">Restricted - Controlled posting</option>
                  </select>
                </div>

                <div className="flex space-x-3">
                  <button
                    type="button"
                    onClick={() => setShowEditChannelModal(false)}
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                    style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '1px solid #B6B09F' }}
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                    style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                  >
                    Update
                  </button>
                </div>
              </form>

              <form className="mt-4 pt-4" style={{ borderTop: '2px solid #B6B09F' }}>
                <button
                  type="submit"
                  onClick={(e) => {
                    e.preventDefault();
                    if (window.confirm('Are you sure you want to delete this channel? All posts will be deleted.')) {
                      console.log('Deleting channel');
                    }
                  }}
                  className="w-full px-4 py-2 rounded-lg font-medium transition-all duration-300"
                  style={{ backgroundColor: '#FEE2E2', color: '#991B1B', border: '1px solid #FCA5A5' }}
                >
                  Delete Channel
                </button>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Create Channel Modal */}
      {showCreateChannelModal && (userRole === 'admin' || userRole === 'moderator') && (
        <div className="fixed inset-0 z-50 overflow-y-auto" style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }}>
          <div className="flex items-center justify-center min-h-screen px-4">
            <div className="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style={{ backgroundColor: '#FFFFFF', border: '2px solid #B6B09F' }}>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-xl font-bold" style={{ color: '#000000' }}>
                  Create Channel
                </h3>
                <button onClick={() => setShowCreateChannelModal(false)} style={{ color: '#B6B09F' }}>
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <form onSubmit={(e) => e.preventDefault()}>
                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Channel Name
                  </label>
                  <input
                    type="text"
                    required
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                    placeholder="e.g., General Chat"
                  />
                </div>

                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Description
                  </label>
                  <textarea
                    rows="3"
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                    placeholder="What is this channel about?"
                  />
                </div>

                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Channel Type
                  </label>
                  <select className="w-full px-4 py-2 rounded-lg" style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}>
                    <option value="general">General - All members can post</option>
                    <option value="announcement">Announcement - Only admins can post</option>
                    <option value="restricted">Restricted - Controlled posting</option>
                  </select>
                </div>

                <div className="flex space-x-3">
                  <button
                    type="button"
                    onClick={() => setShowCreateChannelModal(false)}
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                    style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '1px solid #B6B09F' }}
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                    style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                  >
                    Create
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </Layout>
  );
};

export default CommunityChannel;