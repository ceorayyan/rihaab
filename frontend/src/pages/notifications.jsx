import React, { useState, useEffect } from 'react';
import Layout from '../components/Layout';
import { apiCall, getMediaUrl, formatTimeAgo } from '../config';

const Notifications = () => {
  const [notification, setNotification] = useState(null);
  const [allNotifications, setAllNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchNotifications();
  }, []);

  const fetchNotifications = async () => {
    try {
      setLoading(true);
      setError(null);

      console.log('🔄 Fetching notifications...');

      const data = await apiCall('/key-requests/incoming');
      console.log('📦 Raw notifications data:', data);
      console.log('📦 Type of data:', typeof data);
      console.log('📦 Is array?', Array.isArray(data));

      // Handle different response structures
      let notificationsArray = [];
      
      if (Array.isArray(data)) {
        // Direct array response
        notificationsArray = data;
      } else if (data.success && Array.isArray(data.data)) {
        // Wrapped in success/data
        notificationsArray = data.data;
      } else if (data.data && Array.isArray(data.data)) {
        // Just wrapped in data
        notificationsArray = data.data;
      } else {
        console.error('Unexpected data structure:', data);
        notificationsArray = [];
      }

      console.log('📦 Notifications array:', notificationsArray);

      // Transform the data to match notification format
      const transformedNotifications = notificationsArray.map(request => ({
        id: request.id,
        type: 'key_request',
        user: {
          id: request.sender?.id || request.user?.id,
          username: request.sender?.username || request.user?.username,
          name: request.sender?.name || request.user?.name,
          profile_picture: request.sender?.profile_picture || request.user?.profile_picture
        },
        created_at: request.created_at
      }));
      
      console.log('✅ Transformed notifications:', transformedNotifications);
      setAllNotifications(transformedNotifications);
    } catch (err) {
      console.error('❌ Error fetching notifications:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleAccept = async (id) => {
    try {
      console.log('✅ Accepting key request:', id);
      
      const data = await apiCall(`/key-requests/accept/${id}`, {
        method: 'POST'
      });

      console.log('✅ Accept response:', data);

      setNotification({ type: 'success', message: 'Key request accepted successfully!' });
      
      // Remove the notification from the list
      setAllNotifications(prev => prev.filter(notif => notif.id !== id));
    } catch (err) {
      console.error('❌ Error accepting key request:', err);
      setNotification({ type: 'error', message: err.message || 'Failed to accept key request.' });
    }
  };

  const handleReject = async (id) => {
    try {
      console.log('❌ Rejecting key request:', id);
      
      const data = await apiCall(`/key-requests/reject/${id}`, {
        method: 'POST'
      });

      console.log('❌ Reject response:', data);

      setNotification({ type: 'success', message: 'Key request declined.' });
      
      // Remove the notification from the list
      setAllNotifications(prev => prev.filter(notif => notif.id !== id));
    } catch (err) {
      console.error('❌ Error declining key request:', err);
      setNotification({ type: 'error', message: err.message || 'Failed to decline key request.' });
    }
  };

  const truncateText = (text, limit) => {
    if (!text) return '';
    if (text.length <= limit) return text;
    return text.substring(0, limit) + '...';
  };

  if (loading) {
    return (
      <Layout notification={notification}>
        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2" style={{ borderColor: '#000000' }}></div>
          </div>
        </div>
      </Layout>
    );
  }

  if (error) {
    return (
      <Layout notification={notification}>
        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
          <div className="text-center py-12">
            <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#EF4444' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p className="text-red-600 mb-4">{error}</p>
            <div className="flex gap-4 justify-center">
              <button 
                onClick={fetchNotifications}
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

  return (
    <Layout notification={notification}>
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
        .notification-item {
          animation: fadeIn 0.4s ease-out forwards;
          opacity: 0;
        }
        .notification-item:nth-child(1) { animation-delay: 0.1s; }
        .notification-item:nth-child(2) { animation-delay: 0.2s; }
        .notification-item:nth-child(3) { animation-delay: 0.3s; }
        .notification-item:nth-child(4) { animation-delay: 0.4s; }
        .notification-item:nth-child(n+5) { animation-delay: 0.5s; }
      `}</style>

      <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Header */}
        <div className="mb-6 animate-fade-in">
          <h1 className="text-2xl font-bold" style={{ color: '#000000' }}>Notifications</h1>
          <p className="text-sm mt-1" style={{ color: '#B6B09F' }}>Your activity updates</p>
        </div>

        {/* Notifications List */}
        <div className="space-y-3">
          {allNotifications.length === 0 ? (
            <div className="text-center py-12 animate-fade-in">
              <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#B6B09F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <h3 className="text-xl font-semibold mb-2" style={{ color: '#B6B09F' }}>No Notifications</h3>
              <p style={{ color: '#B6B09F' }}>When someone sends you a key request, you'll see it here.</p>
            </div>
          ) : (
            allNotifications.map((notif) => (
              <div 
                key={notif.id}
                className="notification-item rounded-lg p-4 transition-all duration-300 hover:shadow-lg" 
                style={{ backgroundColor: '#FFFFFF', border: '1px solid #B6B09F' }}
              >
                {/* Key Request Notification */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3 flex-1">
                    {/* Profile Picture */}
                    <div className="shrink-0">
                      <div 
                        className="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110 cursor-pointer" 
                        style={{ border: '2px solid #B6B09F' }}
                      >
                        {notif.user?.profile_picture ? (
                          <img src={getMediaUrl(notif.user.profile_picture)} className="w-full h-full object-cover" alt={notif.user.name} />
                        ) : (
                          <div 
                            className="flex items-center justify-center w-full h-full text-sm font-bold" 
                            style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)', color: '#F2F2F2' }}
                          >
                            {notif.user?.name ? notif.user.name.substring(0, 2).toUpperCase() : 'UN'}
                          </div>
                        )}
                      </div>
                    </div>

                    {/* Text Content */}
                    <div className="flex-1">
                      <p style={{ color: '#000000' }}>
                        <span className="font-semibold hover:underline cursor-pointer">{notif.user?.username || 'Unknown'}</span>
                        <span className="font-normal"> sent you a key request.</span>
                      </p>
                      <p className="text-xs mt-1" style={{ color: '#B6B09F' }}>
                        {formatTimeAgo(notif.created_at)}
                      </p>
                    </div>

                    {/* Request Icon */}
                    <div className="shrink-0 mr-2">
                      <div 
                        className="w-8 h-8 rounded-full flex items-center justify-center" 
                        style={{ backgroundColor: '#EAE4D5' }}
                      >
                        <svg className="w-4 h-4" style={{ color: '#000000' }} fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clipRule="evenodd"/>
                        </svg>
                      </div>
                    </div>
                  </div>

                  {/* Action Buttons */}
                  <div className="flex space-x-2 ml-3">
                    <button 
                      onClick={() => handleAccept(notif.id)}
                      className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                      style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                    >
                      Accept
                    </button>
                    <button 
                      onClick={() => handleReject(notif.id)}
                      className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                      style={{ backgroundColor: '#EAE4D5', color: '#000000', border: '1px solid #B6B09F' }}
                    >
                      Decline
                    </button>
                  </div>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </Layout>
  );
};

export default Notifications;