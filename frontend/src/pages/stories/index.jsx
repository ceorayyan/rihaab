import React, { useState, useEffect } from 'react';

const IndexStories = ({ stories, myStories }) => {
  const [currentUserId] = useState(1); // Replace with actual auth user ID
  const [showMyStories, setShowMyStories] = useState(false);
  const [storyViewerOpen, setStoryViewerOpen] = useState(false);
  const [currentStoryUser, setCurrentStoryUser] = useState(null);
  const [currentStoryIndex, setCurrentStoryIndex] = useState(0);
  const [statsModalOpen, setStatsModalOpen] = useState(false);
  const [progressBars, setProgressBars] = useState([]);

  // Mock data for demonstration
  const mockMyStories = myStories || [
    {
      id: 1,
      type: 'image',
      content: 'path/to/image.jpg',
      caption: 'My story',
      created_at: new Date(Date.now() - 2 * 60 * 60 * 1000),
      views: [{ id: 1 }, { id: 2 }],
    },
  ];

  const mockStories = stories || {
    2: [
      {
        id: 2,
        type: 'video',
        content: 'path/to/video.mp4',
        created_at: new Date(Date.now() - 1 * 60 * 60 * 1000),
        user: { id: 2, name: 'Jane Doe' },
        views: [],
      },
    ],
    3: [
      {
        id: 3,
        type: 'text',
        content: 'This is a text story',
        created_at: new Date(Date.now() - 5 * 60 * 60 * 1000),
        user: { id: 3, name: 'Bob Smith' },
        views: [{ id: 1 }],
      },
    ],
  };

  const hasActiveStories = mockMyStories.length > 0 || Object.keys(mockStories).length > 0;

  const handleMyStory = () => {
    if (mockMyStories.length > 0) {
      setShowMyStories(!showMyStories);
    } else {
      window.location.href = '/stories/create';
    }
  };

  const openStoryViewer = (userId, startIndex = 0) => {
    setCurrentStoryUser(userId);
    setCurrentStoryIndex(startIndex);
    setStoryViewerOpen(true);
  };

  const closeStoryViewer = () => {
    setStoryViewerOpen(false);
    setCurrentStoryUser(null);
    setCurrentStoryIndex(0);
  };

  const nextStory = () => {
    const userStories = currentStoryUser === currentUserId ? mockMyStories : mockStories[currentStoryUser];
    if (currentStoryIndex < userStories.length - 1) {
      setCurrentStoryIndex(currentStoryIndex + 1);
    } else {
      closeStoryViewer();
    }
  };

  const previousStory = () => {
    if (currentStoryIndex > 0) {
      setCurrentStoryIndex(currentStoryIndex - 1);
    }
  };

  const deleteStory = (storyId) => {
    if (window.confirm('Are you sure you want to delete this story?')) {
      console.log('Deleting story:', storyId);
      // Handle delete logic here
    }
  };

  const showStoryStats = () => {
    setStatsModalOpen(true);
  };

  const closeStoryStats = () => {
    setStatsModalOpen(false);
  };

  const deleteCurrentStory = () => {
    const userStories = currentStoryUser === currentUserId ? mockMyStories : mockStories[currentStoryUser];
    const currentStory = userStories[currentStoryIndex];
    deleteStory(currentStory.id);
  };

  const getHoursRemaining = (createdAt) => {
    const now = new Date();
    const created = new Date(createdAt);
    const hoursPassed = Math.floor((now - created) / (1000 * 60 * 60));
    return Math.max(0, 24 - hoursPassed);
  };

  const hasUnviewedStories = (stories) => {
    return stories.some(story => story.views.length === 0);
  };

  return (
    <>
      <style>{`
        .stories-container {
          background-color: #EAE4D5;
          border: 2px solid #B6B09F;
        }
        
        .story-ring-unviewed {
          background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);
        }
        
        .story-ring-viewed {
          background-color: #B6B09F;
        }
        
        .add-story-btn {
          border: 2px solid #B6B09F;
          background-color: #F2F2F2;
          transition: all 0.3s ease;
        }
        
        .add-story-btn:hover {
          border-color: #000000;
          background-color: #EAE4D5;
        }
        
        .story-modal-bg {
          background-color: #000000;
        }
        
        .story-management {
          background-color: #F2F2F2;
          border-top: 2px solid #B6B09F;
        }
        
        .story-grid-item {
          background-color: #EAE4D5;
          border: 2px solid #B6B09F;
          transition: all 0.3s ease;
        }
        
        .story-grid-item:hover {
          border-color: #000000;
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .story-action-btn {
          background-color: #000000;
          color: #EAE4D5;
          transition: all 0.2s ease;
        }
        
        .story-action-btn:hover {
          background-color: #1a1a1a;
        }
        
        .story-delete-btn {
          background-color: #dc3545;
        }
        
        .story-delete-btn:hover {
          background-color: #c82333;
        }

        .scrollbar-hide {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
          display: none;
        }
        
        .progress-bar {
          height: 2px;
          background-color: rgba(182, 176, 159, 0.3);
          border-radius: 2px;
          overflow: hidden;
          flex: 1;
        }
        
        .progress-bar-fill {
          height: 100%;
          background-color: #EAE4D5;
          width: 0%;
          transition: width 0.1s linear;
        }
      `}</style>

      <div className="stories-container rounded-lg p-4 mb-4">
        {!hasActiveStories ? (
          /* No Stories State */
          <div className="flex items-center space-x-4">
            <div className="flex-shrink-0">
              <button onClick={() => (window.location.href = '/stories/create')} className="relative group">
                <div className="add-story-btn w-14 h-14 rounded-full flex items-center justify-center">
                  <svg className="w-6 h-6" style={{ color: '#000000' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6v12m6-6H6"></path>
                  </svg>
                </div>
                <p className="text-xs mt-1 text-center" style={{ color: '#000000' }}>
                  Your story
                </p>
              </button>
            </div>
            <div className="flex-1">
              <p className="text-sm" style={{ color: '#B6B09F' }}>
                No stories to show right now
              </p>
              <a href="/keyrequest/incoming" className="text-xs hover:underline" style={{ color: '#000000' }}>
                Find people to follow
              </a>
            </div>
          </div>
        ) : (
          /* Stories Container */
          <>
            <div className="flex space-x-3 overflow-x-auto scrollbar-hide">
              {/* Add Story / Your Story - Always First */}
              <div className="flex-shrink-0">
                <div className="cursor-pointer" onClick={handleMyStory}>
                  {mockMyStories.length > 0 ? (
                    <div className="relative">
                      <div className="w-14 h-14 rounded-full story-ring-unviewed p-0.5">
                        <div className="w-full h-full rounded-full p-0.5" style={{ backgroundColor: '#EAE4D5' }}>
                          <div className="w-full h-full rounded-full overflow-hidden" style={{ backgroundColor: '#F2F2F2' }}>
                            {mockMyStories[0].type === 'image' ? (
                              <img src={`/storage/${mockMyStories[0].content}`} className="w-full h-full object-cover" alt="Story" />
                            ) : mockMyStories[0].type === 'video' ? (
                              <video className="w-full h-full object-cover">
                                <source src={`/storage/${mockMyStories[0].content}`} type="video/mp4" />
                              </video>
                            ) : (
                              <div className="w-full h-full flex items-center justify-center" style={{ background: 'linear-gradient(135deg, #B6B09F 0%, #000000 100%)' }}>
                                <span className="text-white font-semibold text-xs">U</span>
                              </div>
                            )}
                          </div>
                        </div>
                      </div>
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          window.location.href = '/stories/create';
                        }}
                        className="absolute -bottom-0.5 -right-0.5 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 story-action-btn"
                        style={{ borderColor: '#EAE4D5' }}
                      >
                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M12 4v16m8-8H4"></path>
                        </svg>
                      </button>
                    </div>
                  ) : (
                    <div className="relative">
                      <div className="w-14 h-14 rounded-full add-story-btn flex items-center justify-center">
                        <div className="w-full h-full rounded-full flex items-center justify-center" style={{ backgroundColor: '#F2F2F2' }}>
                          <span className="font-semibold text-xs" style={{ color: '#000000' }}>U</span>
                        </div>
                      </div>
                      <div className="absolute -bottom-0.5 -right-0.5 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 story-action-btn" style={{ borderColor: '#EAE4D5' }}>
                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M12 4v16m8-8H4"></path>
                        </svg>
                      </div>
                    </div>
                  )}
                  <p className="text-xs mt-1 text-center truncate w-14" style={{ color: '#000000' }}>
                    Your story
                  </p>
                </div>
              </div>

              {/* Other Users' Stories */}
              {Object.entries(mockStories).map(([userId, userStories]) => {
                const user = userStories[0].user;
                const hasUnviewed = hasUnviewedStories(userStories);
                const latestStory = userStories[0];

                return (
                  <div key={userId} className="flex-shrink-0">
                    <div className="cursor-pointer" onClick={() => openStoryViewer(parseInt(userId))}>
                      <div className="relative">
                        <div className={`w-14 h-14 rounded-full ${hasUnviewed ? 'story-ring-unviewed' : 'story-ring-viewed'} p-0.5`}>
                          <div className="w-full h-full rounded-full p-0.5" style={{ backgroundColor: '#EAE4D5' }}>
                            <div className="w-full h-full rounded-full overflow-hidden" style={{ backgroundColor: '#F2F2F2' }}>
                              {latestStory.type === 'image' ? (
                                <img src={`/storage/${latestStory.content}`} className="w-full h-full object-cover" alt="Story" />
                              ) : latestStory.type === 'video' ? (
                                <video className="w-full h-full object-cover">
                                  <source src={`/storage/${latestStory.content}`} type="video/mp4" />
                                </video>
                              ) : (
                                <div className="w-full h-full flex items-center justify-center" style={{ background: 'linear-gradient(135deg, #B6B09F 0%, #000000 100%)' }}>
                                  <span className="text-white font-semibold text-xs">{user.name.charAt(0).toUpperCase()}</span>
                                </div>
                              )}
                            </div>
                          </div>
                        </div>
                      </div>
                      <p className="text-xs mt-1 text-center truncate w-14" style={{ color: '#000000' }}>
                        {user.name}
                      </p>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Your Active Stories Management */}
            {mockMyStories.length > 0 && showMyStories && (
              <div className="story-management mt-4 pt-4">
                <div className="flex justify-between items-center mb-3">
                  <h4 className="text-sm font-semibold" style={{ color: '#000000' }}>
                    Your Active Stories
                  </h4>
                  <button onClick={() => setShowMyStories(false)} style={{ color: '#B6B09F' }}>
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  {mockMyStories.slice(0, 6).map((story, index) => (
                    <div key={story.id} className="relative group story-grid-item">
                      <div className="aspect-square rounded-lg overflow-hidden" style={{ backgroundColor: '#F2F2F2' }}>
                        {story.type === 'image' ? (
                          <img src={`/storage/${story.content}`} className="w-full h-full object-cover" alt="Story" />
                        ) : story.type === 'video' ? (
                          <video className="w-full h-full object-cover">
                            <source src={`/storage/${story.content}`} type="video/mp4" />
                          </video>
                        ) : (
                          <div className="w-full h-full flex items-center justify-center p-2">
                            <p className="text-xs text-center" style={{ color: '#000000' }}>
                              {story.content.substring(0, 30)}...
                            </p>
                          </div>
                        )}
                      </div>
                      <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center">
                        <div className="hidden group-hover:flex space-x-2">
                          <button onClick={() => openStoryViewer(currentUserId, index)} className="story-action-btn p-1.5 rounded-full">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                          <button onClick={() => deleteStory(story.id)} className="story-delete-btn text-white p-1.5 rounded-full">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div className="absolute top-1 right-1">
                        <span className="bg-black bg-opacity-60 text-white text-xs px-1 py-0.5 rounded">{story.views.length}</span>
                      </div>
                      <div className="absolute bottom-1 left-1">
                        <span className="bg-black bg-opacity-60 text-white text-xs px-1 py-0.5 rounded">
                          {getHoursRemaining(story.created_at)}h left
                        </span>
                      </div>
                    </div>
                  ))}
                </div>
                {mockMyStories.length > 6 && (
                  <p className="text-xs text-center mt-2" style={{ color: '#B6B09F' }}>
                    +{mockMyStories.length - 6} more stories
                  </p>
                )}
              </div>
            )}
          </>
        )}
      </div>

      {/* Story Viewer Modal */}
      {storyViewerOpen && (
        <div className="fixed inset-0 story-modal-bg z-50">
          <div className="h-full w-full flex items-center justify-center">
            <button onClick={closeStoryViewer} className="absolute top-4 right-4 z-10" style={{ color: '#EAE4D5' }}>
              <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <div className="relative max-w-md w-full h-full md:h-auto md:max-h-[90vh]">
              <div className="absolute top-2 left-2 right-2 flex space-x-1 z-10">
                {(currentStoryUser === currentUserId ? mockMyStories : mockStories[currentStoryUser] || []).map((_, index) => (
                  <div key={index} className="progress-bar">
                    <div className={`progress-bar-fill ${index === currentStoryIndex ? 'w-full' : index < currentStoryIndex ? 'w-full' : 'w-0'}`}></div>
                  </div>
                ))}
              </div>

              <div className="absolute top-6 left-4 right-4 flex items-center z-10">
                <div className="flex items-center flex-1">
                  <div className="w-8 h-8 rounded-full flex items-center justify-center mr-2" style={{ background: 'linear-gradient(135deg, #B6B09F 0%, #000000 100%)' }}>
                    <span className="text-white text-sm font-bold">
                      {currentStoryUser === currentUserId ? 'Y' : mockStories[currentStoryUser]?.[0]?.user?.name?.charAt(0).toUpperCase()}
                    </span>
                  </div>
                  <div>
                    <p className="text-sm font-semibold" style={{ color: '#EAE4D5' }}>
                      {currentStoryUser === currentUserId ? 'You' : mockStories[currentStoryUser]?.[0]?.user?.name}
                    </p>
                    <p className="text-xs" style={{ color: '#B6B09F' }}>
                      {new Date().toLocaleTimeString()}
                    </p>
                  </div>
                </div>

                {currentStoryUser === currentUserId && (
                  <div className="space-x-2">
                    <button onClick={showStoryStats} style={{ color: '#EAE4D5' }}>
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button onClick={deleteCurrentStory} style={{ color: '#EAE4D5' }}>
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                )}
              </div>

              <div className="w-full h-screen md:h-[90vh] md:rounded-lg overflow-hidden" style={{ backgroundColor: '#000000' }}>
                <div className="w-full h-full flex items-center justify-center">
                  {/* Story content will be rendered here based on currentStoryIndex */}
                  <p style={{ color: '#EAE4D5' }}>Story Content</p>
                </div>
              </div>

              <div className="absolute inset-0 flex pointer-events-none">
                <div className="w-1/3 h-full cursor-pointer pointer-events-auto" onClick={previousStory}></div>
                <div className="w-1/3 h-full"></div>
                <div className="w-1/3 h-full cursor-pointer pointer-events-auto" onClick={nextStory}></div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Story Stats Modal */}
      {statsModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-75 z-60">
          <div className="h-full flex items-end md:items-center md:justify-center">
            <div className="rounded-t-xl md:rounded-xl w-full md:max-w-sm max-h-[70vh] overflow-hidden" style={{ backgroundColor: '#EAE4D5' }}>
              <div className="p-4 flex justify-between items-center" style={{ borderBottom: '2px solid #B6B09F' }}>
                <h3 className="text-base font-semibold" style={{ color: '#000000' }}>
                  Views
                </h3>
                <button onClick={closeStoryStats} style={{ color: '#B6B09F' }}>
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <div className="p-4 overflow-y-auto max-h-[calc(70vh-4rem)]">
                <p style={{ color: '#000000' }}>Stats content here</p>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default IndexStories;