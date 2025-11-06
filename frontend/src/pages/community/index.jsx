import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import Layout from '../../Components/Layout';

const CommunityIndex = () => {
  // Mock data - replace with actual API data
  const [myCommunities] = useState([
    {
      id: 1,
      slug: 'tech-enthusiasts',
      name: 'Tech Enthusiasts',
      icon: null,
      members_count: 150,
    },
  ]);

  const [communities] = useState([
    {
      id: 2,
      slug: 'photography-lovers',
      name: 'Photography Lovers',
      description: 'Share your best shots and learn from others',
      icon: null,
      banner: null,
      is_private: false,
      members_count: 234,
    },
    {
      id: 3,
      slug: 'cooking-masters',
      name: 'Cooking Masters',
      description: 'Recipes, tips, and culinary adventures',
      icon: null,
      banner: null,
      is_private: true,
      members_count: 89,
    },
  ]);

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
        .community-card {
          animation: fadeIn 0.4s ease-out forwards;
          opacity: 0;
        }
        .community-card:nth-child(1) { animation-delay: 0.1s; }
        .community-card:nth-child(2) { animation-delay: 0.15s; }
        .community-card:nth-child(3) { animation-delay: 0.2s; }
        .community-card:nth-child(4) { animation-delay: 0.25s; }
        .community-card:nth-child(n+5) { animation-delay: 0.3s; }
      `}</style>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Header */}
        <div className="flex justify-between items-center mb-6 animate-fade-in">
          <div>
            <h1 className="text-3xl font-bold" style={{ color: '#000000' }}>
              Communities
            </h1>
            <p className="text-sm mt-1" style={{ color: '#B6B09F' }}>
              Discover and join amazing communities
            </p>
          </div>
          <Link
            to="/communities/create"
            className="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl flex items-center space-x-2"
            style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create Community</span>
          </Link>
        </div>

        {/* My Communities Section */}
        {myCommunities.length > 0 && (
          <div className="mb-8 animate-fade-in">
            <h2 className="text-xl font-semibold mb-4" style={{ color: '#000000' }}>
              My Communities
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {myCommunities.map((community) => (
                <Link
                  key={community.id}
                  to={`/communities/${community.slug}`}
                  className="community-card group rounded-lg p-4 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                  style={{ backgroundColor: '#FFFFFF', border: '2px solid #B6B09F' }}
                >
                  <div className="flex items-center space-x-3">
                    <div className="w-12 h-12 rounded-lg overflow-hidden shrink-0" style={{ backgroundColor: '#EAE4D5' }}>
                      {community.icon ? (
                        <img src={`/storage/${community.icon}`} className="w-full h-full object-cover" alt={community.name} />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center font-bold text-lg" style={{ color: '#000000' }}>
                          {community.name.substring(0, 2).toUpperCase()}
                        </div>
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold truncate" style={{ color: '#000000' }}>
                        {community.name}
                      </h3>
                      <p className="text-xs" style={{ color: '#B6B09F' }}>
                        {community.members_count} members
                      </p>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        )}

        {/* All Communities Section */}
        <div className="animate-fade-in">
          <h2 className="text-xl font-semibold mb-4" style={{ color: '#000000' }}>
            Discover Communities
          </h2>

          {communities.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {communities.map((community) => (
                <div
                  key={community.id}
                  className="community-card rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl"
                  style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}
                >
                  {/* Banner */}
                  <div className="h-32 relative" style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)' }}>
                    {community.banner && <img src={`/storage/${community.banner}`} className="w-full h-full object-cover" alt="Banner" />}

                    {/* Icon Overlay */}
                    <div className="absolute -bottom-8 left-4">
                      <div className="w-16 h-16 rounded-lg overflow-hidden" style={{ backgroundColor: '#EAE4D5', border: '3px solid #FFFFFF' }}>
                        {community.icon ? (
                          <img src={`/storage/${community.icon}`} className="w-full h-full object-cover" alt={community.name} />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center font-bold text-xl" style={{ color: '#000000' }}>
                            {community.name.substring(0, 2).toUpperCase()}
                          </div>
                        )}
                      </div>
                    </div>
                  </div>

                  {/* Content */}
                  <div className="p-4 pt-10">
                    <div className="flex items-start justify-between mb-2">
                      <div className="flex-1">
                        <h3 className="text-lg font-bold" style={{ color: '#000000' }}>
                          {community.name}
                        </h3>
                        {community.is_private && (
                          <span
                            className="inline-flex items-center px-2 py-1 rounded text-xs font-medium mt-1"
                            style={{ backgroundColor: '#EAE4D5', color: '#000000' }}
                          >
                            <svg className="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                              <path
                                fillRule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clipRule="evenodd"
                              />
                            </svg>
                            Private
                          </span>
                        )}
                      </div>
                    </div>

                    <p className="text-sm mb-4 line-clamp-2" style={{ color: '#B6B09F' }}>
                      {community.description || 'No description available.'}
                    </p>

                    <div className="flex items-center justify-between">
                      <div className="flex items-center space-x-4 text-xs" style={{ color: '#B6B09F' }}>
                        <span className="flex items-center">
                          <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                          </svg>
                          {community.members_count} members
                        </span>
                      </div>

                      <Link
                        to={`/communities/${community.slug}`}
                        className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                        style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                      >
                        View
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-12 rounded-lg" style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}>
              <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
              <h3 className="text-xl font-semibold mb-2" style={{ color: '#B6B09F' }}>
                No Communities Yet
              </h3>
              <p className="mb-4" style={{ color: '#B6B09F' }}>
                Be the first to create a community!
              </p>
              <Link
                to="/communities/create"
                className="inline-block px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
              >
                Create Community
              </Link>
            </div>
          )}
        </div>
      </div>
    </Layout>
  );
};

export default CommunityIndex;