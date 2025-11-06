import React from 'react';
import Layout from '../../Components/Layout';

const ShowStory = ({ story }) => {
  // Mock data for demonstration - replace with actual props/API data
  const mockStory = story || {
    id: 1,
    user: {
      id: 1,
      name: 'John Doe',
    },
    type: 'image',
    content: 'path/to/content',
    caption: 'This is a sample caption for the story.',
    created_at: new Date(),
    user_id: 1,
    views: [
      { id: 1, viewer: { name: 'Jane Smith' } },
      { id: 2, viewer: { name: 'Bob Johnson' } },
      { id: 3, viewer: { name: 'Alice Brown' } },
    ],
  };

  const currentUserId = 1; // Replace with actual auth user ID
  const isOwner = mockStory.user_id === currentUserId;
  const viewersCount = mockStory.views?.length || 0;

  const getTimeRemaining = () => {
    const createdAt = new Date(mockStory.created_at);
    const now = new Date();
    const hoursPassed = Math.floor((now - createdAt) / (1000 * 60 * 60));
    const hoursLeft = 24 - hoursPassed;
    return hoursLeft > 0 ? `${hoursLeft}h` : 'Expired';
  };

  const handleDelete = (e) => {
    e.preventDefault();
    if (window.confirm('Are you sure you want to delete this story?')) {
      // Handle delete logic here
      console.log('Deleting story:', mockStory.id);
    }
  };

  return (
    <Layout>
      <style>{`
        .main-card {
          background-color: #EAE4D5;
          border: 2px solid #B6B09F;
        }
        
        .btn-secondary {
          background-color: #B6B09F;
          color: #000000;
          transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
          background-color: #a09a89;
        }
        
        .btn-danger {
          background-color: #dc3545;
          color: white;
        }
        
        .btn-danger:hover {
          background-color: #c82333;
        }
        
        .user-avatar {
          background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);
        }
        
        .type-badge {
          background-color: #F2F2F2;
          color: #000000;
          border: 1px solid #B6B09F;
        }
        
        .caption-box {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
        }
        
        .stats-card {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
        }
        
        .viewer-badge {
          background-color: #EAE4D5;
          border: 1px solid #B6B09F;
        }
        
        .viewer-avatar {
          background-color: #B6B09F;
        }
        
        .text-story-container {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
        }
      `}</style>

      <div className="py-12">
        <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-6">
            <h2 className="font-semibold text-xl leading-tight" style={{ color: '#000000' }}>
              {mockStory.user.name}'s Story
            </h2>
            <button
              onClick={() => window.history.back()}
              className="btn-secondary font-bold py-2 px-4 rounded text-sm"
            >
              Back to Stories
            </button>
          </div>

          <div className="main-card overflow-hidden shadow-md sm:rounded-lg">
            <div className="p-6">
              {/* Story Header */}
              <div className="flex items-center justify-between mb-6">
                <div className="flex items-center">
                  <div className="w-12 h-12 user-avatar rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                    {mockStory.user.name.charAt(0).toUpperCase()}
                  </div>
                  <div>
                    <h3 className="font-semibold text-lg" style={{ color: '#000000' }}>
                      {mockStory.user.name}
                    </h3>
                    <p className="text-sm" style={{ color: '#B6B09F' }}>
                      {new Date(mockStory.created_at).toLocaleString()}
                    </p>
                  </div>
                </div>
                <div className="text-right">
                  <span className="type-badge inline-block text-xs px-2 py-1 rounded-full mb-1">
                    {mockStory.type.charAt(0).toUpperCase() + mockStory.type.slice(1)}
                  </span>
                  <br />
                  <span className="text-xs" style={{ color: '#B6B09F' }}>
                    {getTimeRemaining() === 'Expired' ? (
                      <span className="text-red-600">Expired</span>
                    ) : (
                      `${getTimeRemaining()} left`
                    )}
                  </span>
                </div>
              </div>

              {/* Story Content */}
              <div className="mb-6">
                {mockStory.type === 'image' && (
                  <div className="text-center">
                    <img
                      src={`/storage/${mockStory.content}`}
                      className="max-w-full max-h-96 mx-auto rounded-lg shadow-md"
                      style={{ border: '2px solid #B6B09F' }}
                      alt="Story content"
                    />
                  </div>
                )}

                {mockStory.type === 'video' && (
                  <div className="text-center">
                    <video
                      controls
                      className="max-w-full max-h-96 mx-auto rounded-lg shadow-md"
                      style={{ border: '2px solid #B6B09F' }}
                    >
                      <source src={`/storage/${mockStory.content}`} type="video/mp4" />
                      Your browser does not support the video tag.
                    </video>
                  </div>
                )}

                {mockStory.type === 'text' && (
                  <div className="text-story-container rounded-lg p-8">
                    <div className="text-center">
                      <svg
                        className="w-12 h-12 mb-4 mx-auto"
                        style={{ color: '#B6B09F' }}
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" />
                      </svg>
                      <div className="prose max-w-none">
                        <p className="text-lg leading-relaxed whitespace-pre-wrap" style={{ color: '#000000' }}>
                          {mockStory.content}
                        </p>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Story Caption */}
              {mockStory.caption && (
                <div className="mb-6">
                  <div className="caption-box rounded-lg p-4">
                    <p style={{ color: '#000000' }}>{mockStory.caption}</p>
                  </div>
                </div>
              )}

              {/* Story Stats (only show to story owner) */}
              {isOwner && (
                <div className="pt-6" style={{ borderTop: '2px solid #B6B09F' }}>
                  <div className="flex items-center justify-between mb-4">
                    <h4 className="font-semibold" style={{ color: '#000000' }}>
                      Story Statistics
                    </h4>
                    <a
                      href={`/stories/${mockStory.id}/viewers`}
                      className="text-sm hover:underline"
                      style={{ color: '#000000' }}
                    >
                      View all viewers →
                    </a>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="stats-card rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold" style={{ color: '#000000' }}>
                        {viewersCount}
                      </div>
                      <div className="text-sm" style={{ color: '#B6B09F' }}>
                        {viewersCount === 1 ? 'View' : 'Views'}
                      </div>
                    </div>
                    <div className="stats-card rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold" style={{ color: '#000000' }}>
                        {getTimeRemaining()}
                      </div>
                      <div className="text-sm" style={{ color: '#B6B09F' }}>
                        Time Remaining
                      </div>
                    </div>
                  </div>

                  {/* Recent Viewers Preview */}
                  {mockStory.views && mockStory.views.length > 0 && (
                    <div className="mt-4">
                      <h5 className="font-medium mb-2" style={{ color: '#000000' }}>
                        Recent Viewers
                      </h5>
                      <div className="flex flex-wrap gap-2">
                        {mockStory.views.slice(0, 5).map((view, index) => (
                          <div
                            key={index}
                            className="viewer-badge flex items-center space-x-1 rounded-full px-3 py-1 text-xs"
                          >
                            <div className="viewer-avatar w-4 h-4 rounded-full flex items-center justify-center text-white text-xs">
                              {view.viewer.name.charAt(0).toUpperCase()}
                            </div>
                            <span style={{ color: '#000000' }}>{view.viewer.name}</span>
                          </div>
                        ))}
                        {mockStory.views.length > 5 && (
                          <span className="text-xs" style={{ color: '#B6B09F' }}>
                            +{mockStory.views.length - 5} more
                          </span>
                        )}
                      </div>
                    </div>
                  )}

                  {/* Delete Story Button */}
                  <div className="mt-6">
                    <form onSubmit={handleDelete}>
                      <button type="submit" className="btn-danger font-bold py-2 px-4 rounded text-sm">
                        Delete Story
                      </button>
                    </form>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default ShowStory;