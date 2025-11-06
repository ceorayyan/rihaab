import React, { useState, useEffect } from 'react';
import Layout from '../components/Layout';
import { apiCall, getMediaUrl } from '../config';

const People = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [notification, setNotification] = useState(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [allUsers, setAllUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const usersPerPage = 10;

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async (search = '') => {
    try {
      setLoading(true);
      setError(null);

      console.log('🔄 Fetching users...');

      let endpoint = '/users';
      if (search) {
        endpoint += `?search=${encodeURIComponent(search)}`;
      }

      const data = await apiCall(endpoint);
      console.log('📦 Raw users data:', data);
      console.log('📦 Type of data:', typeof data);
      console.log('📦 Is array?', Array.isArray(data));

      // Handle different response structures
      let usersArray = [];
      
      if (Array.isArray(data)) {
        // Direct array response
        usersArray = data;
      } else if (data.success && Array.isArray(data.data)) {
        // Wrapped in success/data
        usersArray = data.data;
      } else if (data.data && Array.isArray(data.data)) {
        // Just wrapped in data
        usersArray = data.data;
      } else if (data.users && Array.isArray(data.users)) {
        // Wrapped in users key
        usersArray = data.users;
      } else {
        console.error('Unexpected data structure:', data);
        usersArray = [];
      }

      console.log('✅ Users array:', usersArray);
      console.log('✅ Users count:', usersArray.length);
      
      setAllUsers(usersArray);
    } catch (err) {
      console.error('❌ Error fetching users:', err);
      setError(err.message);
      setAllUsers([]); // Set empty array on error
    } finally {
      setLoading(false);
    }
  };

  // Filter users based on search query (client-side filtering as fallback)
  const filteredUsers = allUsers.filter(user => {
    if (!searchQuery) return true;
    const query = searchQuery.toLowerCase();
    return (
      (user.name && user.name.toLowerCase().includes(query)) || 
      (user.username && user.username.toLowerCase().includes(query))
    );
  });

  console.log('🔍 Filtered users:', filteredUsers.length);

  // Pagination logic
  const indexOfLastUser = currentPage * usersPerPage;
  const indexOfFirstUser = indexOfLastUser - usersPerPage;
  const currentUsers = filteredUsers.slice(indexOfFirstUser, indexOfLastUser);
  const totalPages = Math.ceil(filteredUsers.length / usersPerPage);

  console.log('📄 Current page:', currentPage);
  console.log('📄 Current users:', currentUsers.length);
  console.log('📄 Total pages:', totalPages);

  const handleSearch = (e) => {
    e.preventDefault();
    setCurrentPage(1); // Reset to first page on new search
    fetchUsers(searchQuery); // Fetch with search query
  };

  const handleSendRequest = async (userId, username) => {
    try {
      console.log('📤 Sending key request to:', userId);
      
      const data = await apiCall(`/key-requests/send/${userId}`, {
        method: 'POST'
      });

      console.log('✅ Send request response:', data);

      setNotification({ 
        type: 'success', 
        message: `Key request sent to ${username}!` 
      });

      // Update the user's status in the list
      setAllUsers(prevUsers => 
        prevUsers.map(user => 
          user.id === userId ? { ...user, key_request_status: 'pending' } : user
        )
      );
    } catch (err) {
      console.error('❌ Error sending key request:', err);
      setNotification({ 
        type: 'error', 
        message: err.message || 'Failed to send key request.' 
      });
    }
  };

  const paginate = (pageNumber) => {
    console.log('📄 Navigating to page:', pageNumber);
    setCurrentPage(pageNumber);
  };

  if (loading) {
    return (
      <Layout 
        header={<h2 className="font-semibold text-xl">People</h2>}
        notification={notification}
      >
        <div className="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2" style={{ borderColor: '#000000' }}></div>
          </div>
        </div>
      </Layout>
    );
  }

  if (error) {
    return (
      <Layout 
        header={<h2 className="font-semibold text-xl">People</h2>}
        notification={notification}
      >
        <div className="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-12">
            <svg className="w-16 h-16 mx-auto mb-4" style={{ color: '#EF4444' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p className="text-red-600 mb-4">{error}</p>
            <div className="flex gap-4 justify-center">
              <button 
                onClick={() => fetchUsers(searchQuery)}
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
    <Layout 
      header={<h2 className="font-semibold text-xl">People</h2>}
      notification={notification}
    >
      <div className="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        {/* Search Form */}
        <div className="mb-4">
          <div className="relative">
            <input 
              type="text" 
              placeholder="Search by name or username"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onKeyPress={(e) => {
                if (e.key === 'Enter') {
                  handleSearch(e);
                }
              }}
              className="border rounded-lg px-4 py-3 w-full focus:outline-none focus:ring-2 transition-all duration-300"
              style={{ 
                borderColor: '#B6B09F',
                backgroundColor: '#FFFFFF'
              }}
            />
            <button
              onClick={handleSearch}
              className="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:shadow-lg"
              style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
            >
              Search
            </button>
          </div>
        </div>

        {/* Users List */}
        <div className="bg-white shadow rounded-lg p-4">
          {currentUsers.length > 0 ? (
            currentUsers.map((user, index) => (
              <div 
                key={user.id} 
                className={`flex items-center justify-between py-4 ${
                  index !== currentUsers.length - 1 ? 'border-b' : ''
                }`}
                style={{ borderColor: '#E5E7EB' }}
              >
                <div className="flex items-center space-x-3">
                  {/* Profile Picture */}
                  <div 
                    className="w-12 h-12 rounded-full overflow-hidden shrink-0 transition-transform duration-300 hover:scale-110 cursor-pointer" 
                    style={{ border: '2px solid #B6B09F' }}
                  >
                    {user.profile_picture ? (
                      <img src={getMediaUrl(user.profile_picture)} alt={user.name || 'User'} className="w-full h-full object-cover" />
                    ) : (
                      <div 
                        className="w-full h-full flex items-center justify-center text-white text-sm font-bold"
                        style={{ background: 'linear-gradient(135deg, #000000 0%, #B6B09F 100%)' }}
                      >
                        {user.name ? user.name.substring(0, 2).toUpperCase() : 'U'}
                      </div>
                    )}
                  </div>

                  {/* User Info */}
                  <div>
                    <a 
                      href={`/profile/${user.username}`} 
                      className="font-bold hover:underline transition-colors duration-300"
                      style={{ color: '#000000' }}
                    >
                      {user.name || 'Unknown User'}
                    </a>
                    <div className="text-sm" style={{ color: '#B6B09F' }}>
                      @{user.username || 'unknown'}
                    </div>
                  </div>
                </div>

                {/* Action Button */}
                <div>
                  {(!user.key_request_status || user.key_request_status === null || user.key_request_status === '') && (
                    <button 
                      onClick={() => handleSendRequest(user.id, user.username)}
                      className="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:shadow-lg hover:translate-y-[-2px]"
                      style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
                    >
                      Send Key Request
                    </button>
                  )}
                  {user.key_request_status === 'pending' && (
                    <button 
                      disabled
                      className="px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed opacity-60"
                      style={{ backgroundColor: '#B6B09F', color: '#000000' }}
                    >
                      Request Sent
                    </button>
                  )}
                  {user.key_request_status === 'accepted' && (
                    <button 
                      disabled
                      className="px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed opacity-60"
                      style={{ backgroundColor: '#22C55E', color: '#FFFFFF' }}
                    >
                      Connected
                    </button>
                  )}
                </div>
              </div>
            ))
          ) : (
            <p className="text-center py-8" style={{ color: '#B6B09F' }}>
              {searchQuery ? 'No users found matching your search.' : 'No users found.'}
            </p>
          )}
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="mt-4 flex justify-center items-center space-x-2 flex-wrap gap-2">
            {/* Previous Button */}
            <button
              onClick={() => paginate(currentPage - 1)}
              disabled={currentPage === 1}
              className={`px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 ${
                currentPage === 1 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:shadow-lg hover:translate-y-[-2px]'
              }`}
              style={{ 
                backgroundColor: currentPage === 1 ? '#E5E7EB' : '#EAE4D5', 
                color: '#000000',
                border: '1px solid #B6B09F'
              }}
            >
              Previous
            </button>

            {/* Page Numbers */}
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((pageNum) => (
              <button
                key={pageNum}
                onClick={() => paginate(pageNum)}
                className={`px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:shadow-lg hover:translate-y-[-2px] ${
                  currentPage === pageNum ? 'font-bold' : ''
                }`}
                style={{ 
                  backgroundColor: currentPage === pageNum ? '#000000' : '#EAE4D5',
                  color: currentPage === pageNum ? '#F2F2F2' : '#000000',
                  border: `1px solid ${currentPage === pageNum ? '#000000' : '#B6B09F'}`
                }}
              >
                {pageNum}
              </button>
            ))}

            {/* Next Button */}
            <button
              onClick={() => paginate(currentPage + 1)}
              disabled={currentPage === totalPages}
              className={`px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 ${
                currentPage === totalPages 
                  ? 'opacity-50 cursor-not-allowed' 
                  : 'hover:shadow-lg hover:translate-y-[-2px]'
              }`}
              style={{ 
                backgroundColor: currentPage === totalPages ? '#E5E7EB' : '#EAE4D5', 
                color: '#000000',
                border: '1px solid #B6B09F'
              }}
            >
              Next
            </button>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default People;