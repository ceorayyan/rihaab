import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import Layout from '../../Components/Layout';

const CommunityShow = () => {
  const [showCreateChannelModal, setShowCreateChannelModal] = useState(false);
  
  // Mock data
  const community = {
    id: 1,
    slug: 'tech-enthusiasts',
    name: 'Tech Enthusiasts',
    description: 'A community for technology lovers',
    icon: null,
    banner: null,
    is_private: false,
    members: [{ id: 1 }, { id: 2 }, { id: 3 }],
    channels: [
      { id: 1, slug: 'general', name: 'General', type: 'general' },
      { id: 2, slug: 'announcements', name: 'Announcements', type: 'announcement' },
    ],
    pendingMembers: [],
  };

  const isMember = true;
  const userRole = 'admin'; // 'admin', 'moderator', or 'member'
  const defaultChannel = community.channels[0];

  const [channelForm, setChannelForm] = useState({
    name: '',
    description: '',
    type: 'general',
  });

  const handleChannelSubmit = (e) => {
    e.preventDefault();
    console.log('Creating channel:', channelForm);
    setShowCreateChannelModal(false);
    setChannelForm({ name: '', description: '', type: 'general' });
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
        .active-channel {
          background-color: #EAE4D5 !important;
          font-weight: 600;
        }
      `}</style>

      <div className="flex" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Channels Sidebar */}
        <div className="w-64 shrink-0 animate-fade-in" style={{ backgroundColor: '#FFFFFF', borderRight: '2px solid #B6B09F' }}>
          {/* Community Header */}
          <div className="p-4" style={{ borderBottom: '2px solid #B6B09F' }}>
            <div className="flex items-center space-x-3 mb-3">
              <div className="w-12 h-12 rounded-lg overflow-hidden" style={{ backgroundColor: '#EAE4D5' }}>
                {community.icon ? (
                  <img src={`/storage/${community.icon}`} className="w-full h-full object-cover" alt={community.name} />
                ) : (
                  <div className="w-full h-full flex items-center justify-center font-bold" style={{ color: '#000000' }}>
                    {community.name.substring(0, 2).toUpperCase()}
                  </div>
                )}
              </div>
              <div className="flex-1 min-w-0">
                <h2 className="font-bold truncate" style={{ color: '#000000' }}>
                  {community.name}
                </h2>
                <p className="text-xs" style={{ color: '#B6B09F' }}>
                  {community.members.length} members
                </p>
              </div>
            </div>

            {!isMember && (
              <button
                className="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
              >
                {community.is_private ? 'Request to Join' : 'Join Community'}
              </button>
            )}
          </div>

          {/* Channels List */}
          {isMember && (
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
                {community.channels.map((channel) => (
                  <Link
                    key={channel.id}
                    to={`/communities/${community.slug}/channels/${channel.slug}`}
                    className={`flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 ${
                      defaultChannel && defaultChannel.id === channel.id ? 'active-channel' : ''
                    }`}
                    style={{ color: '#000000' }}
                  >
                    {channel.type === 'announcement' ? (
                      <svg className="w-4 h-4 shrink-0" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                      </svg>
                    ) : (
                      <svg className="w-4 h-4 shrink-0" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clipRule="evenodd" />
                      </svg>
                    )}
                    <span className="text-sm truncate">{channel.name}</span>
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Settings Link (Admin Only) */}
          {isMember && userRole === 'admin' && (
            <div className="p-4" style={{ borderTop: '2px solid #B6B09F' }}>
              <Link
                to={`/communities/${community.slug}/members`}
                className="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 mb-2"
                style={{ color: '#000000' }}
              >
                <svg className="w-4 h-4" style={{ color: '#B6B09F' }} fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span className="text-sm">Members</span>
                {community.pendingMembers.length > 0 && (
                  <span
                    className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full"
                    style={{ backgroundColor: '#F59E0B', color: '#FFFFFF' }}
                  >
                    {community.pendingMembers.length}
                  </span>
                )}
              </Link>
              <Link
                to={`/communities/${community.slug}/settings`}
                className="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1"
                style={{ color: '#000000' }}
              >
                <svg className="w-4 h-4" style={{ color: '#B6B09F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span className="text-sm">Settings</span>
              </Link>
            </div>
          )}
        </div>

        {/* Main Content Area */}
        <div className="flex-1 p-6 animate-fade-in">
          {!isMember ? (
            /* Join Community Prompt */
            <div className="max-w-3xl mx-auto">
              {/* Banner */}
              <div className="h-48 rounded-lg overflow-hidden mb-6" style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)' }}>
                {community.banner && <img src={`/storage/${community.banner}`} className="w-full h-full object-cover" alt="Banner" />}
              </div>

              <div className="text-center rounded-lg p-8" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                <h2 className="text-2xl font-bold mb-2" style={{ color: '#000000' }}>
                  {community.name}
                </h2>
                <p className="mb-6" style={{ color: '#B6B09F' }}>
                  {community.description}
                </p>

                <div className="flex items-center justify-center space-x-6 mb-6 text-sm" style={{ color: '#B6B09F' }}>
                  <span className="flex items-center">
                    <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    {community.members.length} members
                  </span>
                  <span className="flex items-center">
                    <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clipRule="evenodd" />
                    </svg>
                    {community.channels.length} channels
                  </span>
                </div>

                <button
                  className="px-8 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl"
                  style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                >
                  Join Community
                </button>
              </div>
            </div>
          ) : (
            /* Welcome Message - Show first channel or instructions */
            <div className="max-w-4xl mx-auto">
              {defaultChannel && (
                <div className="rounded-lg p-6 mb-4" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                  <h2 className="text-2xl font-bold mb-2" style={{ color: '#000000' }}>
                    Welcome to {community.name}!
                  </h2>
                  <p className="mb-4" style={{ color: '#B6B09F' }}>
                    {community.description}
                  </p>
                  <Link
                    to={`/communities/${community.slug}/channels/${defaultChannel.slug}`}
                    className="inline-block px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                    style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                  >
                    Go to {defaultChannel.name}
                  </Link>
                </div>
              )}

              {/* Recent Activity or Info Cards */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="rounded-lg p-6" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                  <div className="flex items-center space-x-3 mb-3">
                    <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ backgroundColor: '#EAE4D5' }}>
                      <svg className="w-6 h-6" style={{ color: '#000000' }} fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                      </svg>
                    </div>
                    <div>
                      <h3 className="font-bold" style={{ color: '#000000' }}>
                        Members
                      </h3>
                      <p className="text-2xl font-bold" style={{ color: '#000000' }}>
                        {community.members.length}
                      </p>
                    </div>
                  </div>
                  <p className="text-sm" style={{ color: '#B6B09F' }}>
                    Active community members
                  </p>
                </div>

                <div className="rounded-lg p-6" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
                  <div className="flex items-center space-x-3 mb-3">
                    <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ backgroundColor: '#EAE4D5' }}>
                      <svg className="w-6 h-6" style={{ color: '#000000' }} fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clipRule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <h3 className="font-bold" style={{ color: '#000000' }}>
                        Channels
                      </h3>
                      <p className="text-2xl font-bold" style={{ color: '#000000' }}>
                        {community.channels.length}
                      </p>
                    </div>
                  </div>
                  <p className="text-sm" style={{ color: '#B6B09F' }}>
                    Discussion channels
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Create Channel Modal */}
      {isMember && (userRole === 'admin' || userRole === 'moderator') && showCreateChannelModal && (
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

              <form onSubmit={handleChannelSubmit}>
                <div className="mb-4">
                  <label className="block text-sm font-semibold mb-2" style={{ color: '#000000' }}>
                    Channel Name
                  </label>
                  <input
                    type="text"
                    name="name"
                    value={channelForm.name}
                    onChange={(e) => setChannelForm({ ...channelForm, name: e.target.value })}
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
                    name="description"
                    value={channelForm.description}
                    onChange={(e) => setChannelForm({ ...channelForm, description: e.target.value })}
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
                  <select
                    name="type"
                    value={channelForm.type}
                    onChange={(e) => setChannelForm({ ...channelForm, type: e.target.value })}
                    className="w-full px-4 py-2 rounded-lg"
                    style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                  >
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

export default CommunityShow;