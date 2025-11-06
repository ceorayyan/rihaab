import React, { useState, useEffect } from 'react';
import { Heart, MessageCircle, Plus, Send, BarChart3, HelpCircle, Image } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import Layout from '../../components/Layout';
import { apiCall, getMediaUrl, formatTimeAgo } from '../../config';

const formatTimeRemaining = (expiresAt) => {
  const now = new Date();
  const expiresDate = new Date(expiresAt);
  const diffMs = expiresDate - now;

  if (diffMs <= 0) return "Ended";

  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor((diffMs / (1000 * 60 * 60)) % 24);
  const diffMinutes = Math.floor((diffMs / (1000 * 60)) % 60);

  if (diffDays > 0) return `${diffDays} day${diffDays > 1 ? "s" : ""}`;
  if (diffHours > 0) return `${diffHours} hour${diffHours > 1 ? "s" : ""}`;
  if (diffMinutes > 0) return `${diffMinutes} minute${diffMinutes > 1 ? "s" : ""}`;

  return "Less than a minute";
};

const PostsIndex = () => {
  const navigate = useNavigate();
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [notification, setNotification] = useState(null);
  const [commentInputs, setCommentInputs] = useState({});
  const [showComments, setShowComments] = useState({});
  const [userVotes, setUserVotes] = useState({});
  const [votingStates, setVotingStates] = useState({}); // Track loading state per option

  useEffect(() => {
    fetchPosts();
  }, []);

  const fetchPosts = async () => {
    try {
      setLoading(true);
      setError(null);

      const data = await apiCall('/posts');

      if (data.success) {
        setPosts(data.data);
        
        const votes = {};
        data.data.forEach(post => {
          if (post.type === 'poll' && post.poll && post.poll.user_voted_options) {
            votes[post.id] = post.poll.user_voted_options;
          }
        });
        setUserVotes(votes);
      }
    } catch (err) {
      console.error('Error fetching posts:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleLike = async (postId) => {
    try {
      const data = await apiCall(`/posts/${postId}/like`, {
        method: 'POST',
      });

      if (data.success) {
        setPosts(posts.map(p => 
          p.id === postId 
            ? { 
                ...p, 
                is_liked: data.liked,
                likes_count: data.likes_count 
              } 
            : p
        ));
      }
    } catch (err) {
      console.error('Error liking post:', err);
    }
  };

  const handleComment = async (postId) => {
    const content = commentInputs[postId];
    
    if (!content || !content.trim()) {
      return;
    }

    try {
      const data = await apiCall(`/posts/${postId}/comment`, {
        method: 'POST',
        body: JSON.stringify({ content: content.trim() }),
      });

      if (data.success) {
        setPosts(posts.map(p => 
          p.id === postId 
            ? { 
                ...p, 
                comments_count: p.comments_count + 1,
                comments: [...(p.comments || []), data.data]
              } 
            : p
        ));

        setCommentInputs({ ...commentInputs, [postId]: '' });
      }
    } catch (err) {
      console.error('Error adding comment:', err);
    }
  };

  const handlePollVote = async (postId, optionId) => {
    const voteKey = `${postId}-${optionId}`;
    
    // Prevent double-clicking
    if (votingStates[voteKey]) return;
    
    setVotingStates({ ...votingStates, [voteKey]: true });
    
    const currentVotes = userVotes[postId] || [];
    const isVoted = currentVotes.includes(optionId);
    
    if (isVoted) {
      // Unvote
      const newVotes = currentVotes.filter(id => id !== optionId);
      setUserVotes({ ...userVotes, [postId]: newVotes });
      
      setPosts(posts.map(p => {
        if (p.id === postId && p.poll) {
          return {
            ...p,
            poll: {
              ...p.poll,
              user_has_voted: newVotes.length > 0,
              options: p.poll.options.map(opt => 
                opt.id === optionId 
                  ? { ...opt, votes_count: Math.max(0, opt.votes_count - 1) }
                  : opt
              )
            }
          };
        }
        return p;
      }));
      
      try {
        const data = await apiCall(`/posts/${postId}/poll/unvote`, {
          method: 'POST',
          body: JSON.stringify({ option_id: optionId }),
        });

        if (data.success && data.poll) {
          setPosts(posts.map(p => 
            p.id === postId 
              ? { ...p, poll: data.poll }
              : p
          ));
          setUserVotes({ ...userVotes, [postId]: data.poll.user_voted_options || [] });
        }
      } catch (err) {
        // Revert on error
        setUserVotes({ ...userVotes, [postId]: currentVotes });
        setPosts(posts.map(p => {
          if (p.id === postId && p.poll) {
            return {
              ...p,
              poll: {
                ...p.poll,
                options: p.poll.options.map(opt => 
                  opt.id === optionId 
                    ? { ...opt, votes_count: opt.votes_count + 1 }
                    : opt
                )
              }
            };
          }
          return p;
        }));
      }
    } else {
      // Vote
      const newVotes = [...currentVotes, optionId];
      setUserVotes({ ...userVotes, [postId]: newVotes });
      
      setPosts(posts.map(p => {
        if (p.id === postId && p.poll) {
          return {
            ...p,
            poll: {
              ...p.poll,
              user_has_voted: true,
              options: p.poll.options.map(opt => 
                opt.id === optionId 
                  ? { ...opt, votes_count: opt.votes_count + 1 }
                  : opt
              )
            }
          };
        }
        return p;
      }));
      
      try {
        const data = await apiCall(`/posts/${postId}/poll/vote`, {
          method: 'POST',
          body: JSON.stringify({ option_id: optionId }),
        });

        if (data.success && data.poll) {
          setPosts(posts.map(p => 
            p.id === postId 
              ? { ...p, poll: data.poll }
              : p
          ));
          setUserVotes({ ...userVotes, [postId]: data.poll.user_voted_options || [] });
        }
      } catch (err) {
        // Revert on error
        setUserVotes({ ...userVotes, [postId]: currentVotes });
        setPosts(posts.map(p => {
          if (p.id === postId && p.poll) {
            return {
              ...p,
              poll: {
                ...p.poll,
                user_has_voted: currentVotes.length > 0,
                options: p.poll.options.map(opt => 
                  opt.id === optionId 
                    ? { ...opt, votes_count: Math.max(0, opt.votes_count - 1) }
                    : opt
                )
              }
            };
          }
          return p;
        }));
      }
    }
    
    // Remove loading state after a short delay
    setTimeout(() => {
      setVotingStates(prev => {
        const newState = { ...prev };
        delete newState[voteKey];
        return newState;
      });
    }, 300);
  };

  const toggleComments = (postId) => {
    setShowComments({ 
      ...showComments, 
      [postId]: !showComments[postId] 
    });
  };

  const getPostTypeIcon = (type) => {
    switch(type) {
      case 'poll':
        return <BarChart3 size={16} className="text-blue-600" />;
      case 'qa':
        return <HelpCircle size={16} className="text-purple-600" />;
      default:
        return null;
    }
  };

  const getPostTypeLabel = (type) => {
    switch(type) {
      case 'poll':
        return 'Poll';
      case 'qa':
        return 'Question';
      default:
        return 'Post';
    }
  };

  if (loading) {
    return (
      <Layout notification={notification}>
        <div className="max-w-2xl mx-auto px-4 py-6">
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-black"></div>
          </div>
        </div>
      </Layout>
    );
  }

  return (
    <Layout notification={notification}>
      <div className="max-w-2xl mx-auto px-4 py-6 space-y-6">
        
        {/* Create Post Card */}
        <div className="bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-gray-400 rounded-2xl shadow-lg p-6">
          <div className="flex items-center space-x-3 mb-4">
            <div className="w-12 h-12 rounded-full bg-gradient-to-br from-gray-700 to-black flex items-center justify-center text-white font-bold shadow-md">
              {JSON.parse(localStorage.getItem('user') || '{}')?.name?.substring(0, 2).toUpperCase() || 'U'}
            </div>
            <button 
              onClick={() => navigate('/posts/create')}
              className="flex-1 bg-white border-2 border-gray-300 rounded-full px-5 py-3 text-gray-500 hover:bg-gray-50 hover:border-gray-400 transition text-left font-medium shadow-sm"
            >
              What's on your mind?
            </button>
          </div>
          <div className="grid grid-cols-3 gap-3">
            <button 
              onClick={() => navigate('/posts/create')}
              className="flex items-center justify-center space-x-2 px-4 py-3 rounded-xl bg-white border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition shadow-sm"
            >
              <Image size={20} className="text-green-600" /> 
              <span className="text-sm font-semibold">Photo</span>
            </button>
            <button 
              onClick={() => navigate('/posts/create')}
              className="flex items-center justify-center space-x-2 px-4 py-3 rounded-xl bg-white border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition shadow-sm"
            >
              <BarChart3 size={20} className="text-blue-600" /> 
              <span className="text-sm font-semibold">Poll</span>
            </button>
            <button 
              onClick={() => navigate('/posts/create')}
              className="flex items-center justify-center space-x-2 px-4 py-3 rounded-xl bg-white border-2 border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition shadow-sm"
            >
              <HelpCircle size={20} className="text-purple-600" /> 
              <span className="text-sm font-semibold">Q&A</span>
            </button>
          </div>
        </div>

        {error && (
          <div className="bg-red-100 border-2 border-red-400 rounded-xl p-4 shadow-md">
            <p className="text-red-700 font-medium">{error}</p>
            <button 
              onClick={fetchPosts}
              className="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium"
            >
              Retry
            </button>
          </div>
        )}

        {/* Posts Feed */}
        <div className="space-y-6">
          {posts.length === 0 ? (
            <div className="bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-gray-400 rounded-2xl p-12 text-center shadow-lg">
              <div className="text-6xl mb-4">📝</div>
              <p className="text-gray-700 text-lg mb-4 font-medium">No posts yet. Be the first to share something!</p>
              <button 
                onClick={() => navigate('/posts/create')}
                className="bg-black text-yellow-100 px-8 py-3 rounded-xl font-semibold hover:bg-gray-800 transition shadow-lg"
              >
                Create Post
              </button>
            </div>
          ) : (
            posts.map(post => {
              const isAnonymous = post.type === 'qa' && post.qa && post.qa.anonymous;
              
              return (
                <article key={post.id} className="bg-white border-2 border-gray-300 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                  {/* Post Header */}
                  <div className="p-6 pb-4">
                    <div className="flex items-start justify-between mb-4">
                      <div className="flex items-center space-x-3">
                        <div className="w-12 h-12 rounded-full overflow-hidden border-2 border-gray-300 shadow-md">
                          {isAnonymous ? (
                            <div className="w-full h-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                              ?
                            </div>
                          ) : post.user.profile_picture ? (
                            <img 
                              src={getMediaUrl(post.user.profile_picture)} 
                              alt={post.user.name}
                              className="w-full h-full object-cover"
                            />
                          ) : (
                            <div className="w-full h-full bg-gradient-to-br from-gray-600 to-black flex items-center justify-center text-white font-bold">
                              {post.user.name.substring(0, 2).toUpperCase()}
                            </div>
                          )}
                        </div>
                        <div>
                          <h3 className="font-bold text-base text-gray-900">
                            {isAnonymous ? 'Anonymous' : post.user.name}
                          </h3>
                          <p className="text-xs text-gray-500">
                            {formatTimeAgo(post.created_at)}
                          </p>
                        </div>
                      </div>
                      
                      {post.type !== 'standard' && (
                        <div className="flex items-center space-x-1 px-3 py-1 rounded-full bg-gray-100 border border-gray-300">
                          {getPostTypeIcon(post.type)}
                          <span className="text-xs font-semibold text-gray-700">{getPostTypeLabel(post.type)}</span>
                        </div>
                      )}
                    </div>
                    
                    {/* Standard Post Content */}
                    {post.type === 'standard' && (
                      <>
                        <p className="leading-relaxed text-base text-gray-800 whitespace-pre-line mb-4">
                          {post.content}
                        </p>
                        {post.media_path && (
                          <div className="mt-4 rounded-xl overflow-hidden border-2 border-gray-300 shadow-md">
                            {post.media_type && post.media_type.startsWith('image') ? (
                              <img 
                                src={post.media_path} 
                                alt="Post media"
                                className="w-full h-auto"
                              />
                            ) : post.media_type && post.media_type.startsWith('video') ? (
                              <video 
                                src={post.media_path} 
                                controls
                                className="w-full h-auto bg-black"
                              />
                            ) : null}
                          </div>
                        )}
                      </>
                    )}

                    {/* Poll Content */}
                    {post.type === 'poll' && post.poll && (
                      <div className="space-y-4">
                        <div className="flex items-start justify-between">
                          <h3 className="text-lg font-bold text-gray-900 flex-1">
                            {post.poll.question}
                          </h3>
                          {post.poll.allow_multiple && !post.poll.is_expired && (
                            <span className="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold ml-3">
                              Multiple choice
                            </span>
                          )}
                        </div>
                        
                        <div className="space-y-2">
                          {post.poll.options.map((option) => {
                            const totalVotes = post.poll.options.reduce((sum, opt) => sum + opt.votes_count, 0);
                            const percentage = totalVotes > 0 ? Math.round((option.votes_count / totalVotes) * 100) : 0;
                            const hasVoted = post.poll.user_has_voted || post.poll.is_expired;
                            const isVotedByUser = (userVotes[post.id] || []).includes(option.id);
                            const voteKey = `${post.id}-${option.id}`;
                            const isVoting = votingStates[voteKey];

                            return (
                              <button
                                key={option.id}
                                onClick={() => !post.poll.is_expired && handlePollVote(post.id, option.id)}
                                disabled={post.poll.is_expired || isVoting}
                                className={`w-full group relative overflow-hidden rounded-xl transition-all duration-300 ${
                                  post.poll.is_expired
                                    ? 'cursor-not-allowed'
                                    : 'cursor-pointer hover:scale-[1.02]'
                                }`}
                              >
                                {/* Background bar */}
                                <div className={`absolute inset-0 transition-all duration-500 ${
                                  isVotedByUser 
                                    ? 'bg-gradient-to-r from-green-400 to-green-500' 
                                    : hasVoted
                                    ? 'bg-gradient-to-r from-gray-100 to-gray-200'
                                    : 'bg-gray-50 group-hover:bg-gray-100'
                                }`} style={{ width: hasVoted ? `${percentage}%` : '100%' }} />
                                
                                {/* Content */}
                                <div className={`relative px-5 py-4 flex items-center justify-between border-2 rounded-xl transition-all ${
                                  isVotedByUser
                                    ? 'border-green-500'
                                    : hasVoted
                                    ? 'border-gray-300'
                                    : 'border-gray-300 group-hover:border-gray-400'
                                }`}>
                                  <div className="flex items-center space-x-3 flex-1">
                                    <span className={`font-semibold text-sm ${
                                      isVotedByUser ? 'text-white' : 'text-gray-900'
                                    }`}>
                                      {option.text}
                                    </span>
                                    {isVotedByUser && !post.poll.is_expired && (
                                      <span className="text-white text-xs bg-white bg-opacity-30 px-2 py-0.5 rounded-full">
                                        ✓ Voted
                                      </span>
                                    )}
                                  </div>
                                  
                                  {hasVoted && (
                                    <div className="flex items-center space-x-4">
                                      <div className="text-right">
                                        <div className={`text-lg font-bold ${
                                          isVotedByUser ? 'text-white' : 'text-gray-900'
                                        }`}>
                                          {percentage}%
                                        </div>
                                        <div className={`text-xs ${
                                          isVotedByUser ? 'text-white text-opacity-90' : 'text-gray-500'
                                        }`}>
                                          {option.votes_count} {option.votes_count === 1 ? 'vote' : 'votes'}
                                        </div>
                                      </div>
                                    </div>
                                  )}
                                </div>
                              </button>
                            );
                          })}
                        </div>

                        <div className="flex items-center justify-between pt-3 border-t-2 border-gray-200 mt-4">
                          <p className="text-sm text-gray-600 font-medium">
                            {post.poll.is_expired ? (
                              <span className="text-red-600">Poll ended</span>
                            ) : (
                              <span>Ends in {formatTimeRemaining(post.poll.expires_at)}</span>
                            )}
                          </p>
                          <p className="text-sm text-gray-600 font-medium">
                            {post.poll.options.reduce((sum, opt) => sum + opt.votes_count, 0)} total votes
                          </p>
                        </div>
                      </div>
                    )}

                    {/* Q&A Content */}
                    {post.type === 'qa' && post.qa && (
                      <div className="space-y-4">
                        <div className="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-5">
                          <h3 className="text-lg font-bold text-gray-900 mb-3">
                            {post.content}
                          </h3>
                          
                          {post.qa.details && (
                            <p className="text-sm text-gray-700 leading-relaxed mb-3">
                              {post.qa.details}
                            </p>
                          )}
                          
                          <div className="flex items-center space-x-3 flex-wrap gap-2">
                            {post.qa.category && (
                              <span className="px-3 py-1 bg-white border border-purple-300 rounded-full text-xs font-semibold text-purple-700">
                                {post.qa.category}
                              </span>
                            )}
                            {post.qa.anonymous && (
                              <span className="px-3 py-1 bg-gray-200 border border-gray-300 rounded-full text-xs font-semibold text-gray-700">
                                Anonymous
                              </span>
                            )}
                          </div>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Post Actions */}
                  <div className="px-6 py-4 bg-gray-50 border-t-2 border-gray-200 flex items-center space-x-6">
                    <button 
                      onClick={() => handleLike(post.id)}
                      className={`flex items-center space-x-2 px-4 py-2 rounded-xl hover:bg-white transition-all group ${
                        post.is_liked ? 'text-red-500' : 'text-gray-600'
                      }`}
                    >
                      <Heart 
                        size={20} 
                        className={`transition-all ${
                          post.is_liked ? 'fill-red-500 text-red-500' : 'group-hover:fill-red-400 group-hover:text-red-400'
                        }`}
                      /> 
                      <span className="text-sm font-semibold">Like</span>
                      {post.likes_count > 0 && (
                        <span className="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-bold">
                          {post.likes_count}
                        </span>
                      )}
                    </button>
                    
                    <button 
                      onClick={() => toggleComments(post.id)}
                      className="flex items-center space-x-2 px-4 py-2 rounded-xl hover:bg-white transition-all text-gray-600 hover:text-blue-600 group"
                    >
                      <MessageCircle size={20} className="group-hover:text-blue-600 transition-colors" /> 
                      <span className="text-sm font-semibold">Comment</span>
                      {post.comments_count > 0 && (
                        <span className="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-bold">
                          {post.comments_count}
                        </span>
                      )}
                    </button>
                  </div>

                  {/* Comments Section */}
                  {showComments[post.id] && (
                    <div className="px-6 pb-6 bg-gray-50 border-t-2 border-gray-200 pt-4 space-y-4">
                      <div className="flex items-center space-x-3">
                        <input
                          type="text"
                          placeholder="Write a comment..."
                          value={commentInputs[post.id] || ''}
                          onChange={(e) => setCommentInputs({ 
                            ...commentInputs, 
                            [post.id]: e.target.value 
                          })}
                          onKeyPress={(e) => {
                            if (e.key === 'Enter') {
                              handleComment(post.id);
                            }
                          }}
                          className="flex-1 px-4 py-3 bg-white border-2 border-gray-300 rounded-full focus:border-black outline-none transition shadow-sm"
                        />
                        <button
                          onClick={() => handleComment(post.id)}
                          className="p-3 bg-black text-yellow-100 rounded-full hover:bg-gray-800 transition shadow-md"
                        >
                          <Send size={18} />
                        </button>
                      </div>

                      {post.comments && post.comments.length > 0 && (
                        <div className="space-y-3 mt-4">
                          {post.comments.map((comment) => (
                            <div key={comment.id} className="flex space-x-3">
                              <div className="w-9 h-9 rounded-full overflow-hidden border-2 border-gray-300 shrink-0 shadow-sm">
                                {comment.user.profile_picture ? (
                                  <img 
                                    src={getMediaUrl(comment.user.profile_picture)} 
                                    alt={comment.user.name}
                                    className="w-full h-full object-cover"
                                  />
                                ) : (
                                  <div className="w-full h-full bg-gradient-to-br from-gray-500 to-black flex items-center justify-center text-white font-bold text-xs">
                                    {comment.user.name.substring(0, 2).toUpperCase()}
                                  </div>
                                )}
                              </div>
                              <div className="flex-1">
                                <div className="bg-white border border-gray-200 rounded-2xl px-4 py-2 shadow-sm">
                                  <p className="font-bold text-sm text-gray-900">{comment.user.name}</p>
                                  <p className="text-sm text-gray-700">{comment.content}</p>
                                </div>
                                <p className="text-xs text-gray-500 mt-1 ml-3">
                                  {formatTimeAgo(comment.created_at)}
                                </p>
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  )}
                </article>
              );
            })
          )}
        </div>
      </div>
    </Layout>
  );
};

export default PostsIndex;