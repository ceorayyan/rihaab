// KEY FIXES APPLIED:
// 1. Changed 'post_type' to 'type' for both poll and Q&A
// 2. Added parseInt() for poll_duration
// 3. All API calls now match backend expectations

import React, { useState } from 'react';
import { X, Plus, Zap, Compass } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import Layout from '../../components/Layout';
import { API_BASE_URL, getAuthToken } from '../../config';

const CreatePost = () => {
  const navigate = useNavigate();
  const [emojiPickerOpen, setEmojiPickerOpen] = useState(false);
  const [mediaFile, setMediaFile] = useState(null);
  const [mediaPreview, setMediaPreview] = useState(null);
  const [pollOptions, setPollOptions] = useState(['', '']);
  const [activeTab, setActiveTab] = useState('standard');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [formData, setFormData] = useState({
    content: '',
    pollQuestion: '',
    pollDuration: '7',
    allowMultiple: false,
    qaQuestion: '',
    qaDetails: '',
    qaCategory: '',
    qaAnonymous: false,
    privacy: 'public'
  });

  const emojis = ['😀', '😂', '😍', '🥰', '😎', '🤔', '😢', '😡', '👍', '👎', '❤️', '🔥', '✨', '🎉', '💯', '🚀'];

  const handleMediaChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setMediaFile(file);
      
      const reader = new FileReader();
      reader.onload = (event) => {
        setMediaPreview({
          type: file.type.startsWith('image/') ? 'image' : 'video',
          src: event.target.result,
          name: file.name
        });
      };
      reader.readAsDataURL(file);
    }
  };

  const addPollOption = () => {
    if (pollOptions.length < 10) {
      setPollOptions([...pollOptions, '']);
    }
  };

  const removePollOption = (index) => {
    if (pollOptions.length > 2) {
      setPollOptions(pollOptions.filter((_, i) => i !== index));
    }
  };

  const updatePollOption = (index, value) => {
    const newOptions = [...pollOptions];
    newOptions[index] = value;
    setPollOptions(newOptions);
  };

  const insertEmoji = (emoji) => {
    const textarea = document.getElementById('standard_content');
    if (textarea) {
      const cursorPos = textarea.selectionStart;
      const newText = textarea.value.substring(0, cursorPos) + emoji + textarea.value.substring(cursorPos);
      setFormData({ ...formData, content: newText });
      textarea.focus();
      textarea.selectionStart = textarea.selectionEnd = cursorPos + emoji.length;
    }
    setEmojiPickerOpen(false);
  };

  const handlePost = async () => {
    try {
      setLoading(true);
      setError(null);

      const token = getAuthToken();

      if (activeTab === 'standard') {
        if (!formData.content.trim() && !mediaFile) {
          setError('Please add content or media to your post');
          setLoading(false);
          return;
        }

        const postData = new FormData();
        postData.append('content', formData.content);
        postData.append('privacy', formData.privacy);
        postData.append('type', 'standard');
        
        if (mediaFile) {
          postData.append('media', mediaFile);
        }

        const response = await fetch(`${API_BASE_URL}/posts`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
          body: postData,
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Failed to create post');
        }

        if (data.success) {
          navigate('/posts');
        }
      } else if (activeTab === 'poll') {
        // FIXED: Validate poll
        if (!formData.pollQuestion.trim()) {
          setError('Please enter a poll question');
          setLoading(false);
          return;
        }

        const validOptions = pollOptions.filter(opt => opt.trim());
        if (validOptions.length < 2) {
          setError('Please provide at least 2 poll options');
          setLoading(false);
          return;
        }

        // FIXED: Changed post_type to type, added parseInt for duration
        const response = await fetch(`${API_BASE_URL}/posts`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            type: 'poll',  // FIXED: was post_type
            poll_question: formData.pollQuestion,
            poll_options: validOptions,
            poll_duration: parseInt(formData.pollDuration),  // FIXED: added parseInt
            allow_multiple: formData.allowMultiple,
            privacy: formData.privacy,
          }),
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Failed to create poll');
        }

        if (data.success) {
          navigate('/posts');
        }
      } else if (activeTab === 'qa') {
        // FIXED: Validate Q&A
        if (!formData.qaQuestion.trim()) {
          setError('Please enter a question');
          setLoading(false);
          return;
        }

        // FIXED: Changed post_type to type
        const response = await fetch(`${API_BASE_URL}/posts`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            type: 'qa',  // FIXED: was post_type
            qa_question: formData.qaQuestion,
            qa_details: formData.qaDetails,
            qa_category: formData.qaCategory,
            qa_anonymous: formData.qaAnonymous,
            privacy: formData.privacy,
          }),
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Failed to create question');
        }

        if (data.success) {
          navigate('/posts');
        }
      }
    } catch (err) {
      console.error('Error creating post:', err);
      setError(err.message || 'Failed to create post. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Create Post
        </h2>
      }
    >
      <div className="max-w-4xl mx-auto px-4 py-6">
        <div className="bg-yellow-100 border-2 border-gray-400 rounded-xl shadow-lg p-6 space-y-6">
          
          {error && (
            <div className="bg-red-100 border-2 border-red-400 rounded-lg p-4">
              <p className="text-red-700">{error}</p>
            </div>
          )}

          <div className="flex flex-wrap gap-3">
            {[
              { id: 'standard', label: 'Standard Post', icon: Plus },
              { id: 'poll', label: 'Poll', icon: Zap },
              { id: 'qa', label: 'Q&A', icon: Compass }
            ].map(tab => {
              const IconComponent = tab.icon;
              return (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  disabled={loading}
                  className={`px-6 py-2 rounded-lg font-medium transition flex items-center gap-2 ${
                    activeTab === tab.id
                      ? 'bg-black text-yellow-100'
                      : 'bg-gray-100 border-2 border-gray-400 hover:bg-yellow-100'
                  } ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
                >
                  <IconComponent size={20} />
                  {tab.label}
                </button>
              );
            })}
          </div>

          {activeTab === 'standard' && (
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold mb-2">What's on your mind?</label>
                <textarea
                  id="standard_content"
                  className="w-full p-4 bg-gray-100 border-2 border-gray-400 rounded-lg resize-none focus:border-black outline-none transition"
                  rows="6"
                  placeholder="Share your thoughts, ideas, or announcements..."
                  value={formData.content}
                  onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                  disabled={loading}
                />
                <p className="text-xs text-gray-600 mt-1">{formData.content.length} / 5000 characters</p>
              </div>

              <div>
                <label className="block text-sm font-semibold mb-2">Add Media (Optional)</label>
                <label className={`border-2 border-dashed border-gray-400 rounded-lg p-6 text-center cursor-pointer hover:bg-white transition block ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}>
                  <div className="flex justify-center mb-2 text-4xl">📸</div>
                  <p className="text-sm font-medium">Click to upload photo or video</p>
                  <p className="text-xs text-gray-600 mt-1">PNG, JPG, GIF, MP4 up to 10MB</p>
                  <input 
                    type="file" 
                    className="hidden" 
                    accept="image/*,video/*" 
                    onChange={handleMediaChange}
                    disabled={loading}
                  />
                </label>
                
                {mediaPreview && (
                  <div className="mt-4 relative">
                    <button
                      type="button"
                      onClick={() => {
                        setMediaPreview(null);
                        setMediaFile(null);
                      }}
                      disabled={loading}
                      className="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 z-10"
                    >
                      <X size={16} />
                    </button>
                    {mediaPreview.type === 'image' ? (
                      <img src={mediaPreview.src} alt="Preview" className="w-full h-auto rounded-lg border-2 border-gray-400 max-h-96 object-cover" />
                    ) : (
                      <video src={mediaPreview.src} controls className="w-full h-auto rounded-lg border-2 border-gray-400 max-h-96" />
                    )}
                    <p className="text-xs text-gray-600 mt-2">{mediaPreview.name}</p>
                  </div>
                )}
              </div>

              <div>
                <button
                  type="button"
                  onClick={() => setEmojiPickerOpen(!emojiPickerOpen)}
                  disabled={loading}
                  className="p-4 bg-gray-100 border-2 border-gray-400 rounded-lg text-center hover:border-black transition"
                >
                  <div className="text-2xl mb-1">😊</div>
                  <p className="text-xs font-medium">Add Emoji</p>
                </button>
              </div>

              {emojiPickerOpen && (
                <div className="bg-gray-100 border-2 border-gray-400 rounded-lg p-4 grid grid-cols-8 gap-2">
                  {emojis.map(emoji => (
                    <button
                      key={emoji}
                      type="button"
                      onClick={() => insertEmoji(emoji)}
                      className="text-2xl hover:scale-125 transition"
                    >
                      {emoji}
                    </button>
                  ))}
                </div>
              )}
            </div>
          )}

          {activeTab === 'poll' && (
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold mb-2">Poll Question</label>
                <input 
                  type="text" 
                  className="w-full p-3 bg-gray-100 border-2 border-gray-400 rounded-lg focus:border-black outline-none transition" 
                  placeholder="Ask a question..." 
                  value={formData.pollQuestion}
                  onChange={(e) => setFormData({ ...formData, pollQuestion: e.target.value })}
                  disabled={loading}
                  maxLength={500}
                />
              </div>

              <div>
                <label className="block text-sm font-semibold mb-2">Poll Options</label>
                <div className="space-y-2">
                  {pollOptions.map((option, i) => (
                    <div key={i} className="flex items-center gap-3 p-3 bg-gray-100 border-2 border-gray-400 rounded-lg">
                      <span className="font-semibold text-gray-600 min-w-fit">{i + 1}.</span>
                      <input 
                        type="text" 
                        className="flex-1 bg-transparent outline-none" 
                        placeholder={`Option ${i + 1}`}
                        value={option}
                        onChange={(e) => updatePollOption(i, e.target.value)}
                        disabled={loading}
                        maxLength={200}
                      />
                      {pollOptions.length > 2 && (
                        <button 
                          type="button"
                          onClick={() => removePollOption(i)}
                          disabled={loading}
                          className="bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition"
                        >
                          <X size={16} />
                        </button>
                      )}
                    </div>
                  ))}
                </div>
                {pollOptions.length < 10 && (
                  <button 
                    type="button"
                    onClick={addPollOption}
                    disabled={loading}
                    className="mt-3 bg-black text-yellow-100 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition"
                  >
                    + Add Option
                  </button>
                )}
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-semibold mb-2">Poll Duration</label>
                  <select 
                    className="w-full p-3 bg-gray-100 border-2 border-gray-400 rounded-lg focus:border-black outline-none"
                    value={formData.pollDuration}
                    onChange={(e) => setFormData({ ...formData, pollDuration: e.target.value })}
                    disabled={loading}
                  >
                    <option value="1">1 Day</option>
                    <option value="3">3 Days</option>
                    <option value="7">7 Days</option>
                    <option value="14">14 Days</option>
                    <option value="30">30 Days</option>
                  </select>
                </div>
                <div className="flex items-end">
                  <label className="flex items-center space-x-2 cursor-pointer">
                    <input 
                      type="checkbox" 
                      className="w-4 h-4"
                      checked={formData.allowMultiple}
                      onChange={(e) => setFormData({ ...formData, allowMultiple: e.target.checked })}
                      disabled={loading}
                    />
                    <span className="text-sm">Allow multiple choices</span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'qa' && (
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold mb-2">Your Question</label>
                <textarea 
                  className="w-full p-4 bg-gray-100 border-2 border-gray-400 rounded-lg resize-none focus:border-black outline-none transition" 
                  rows="4" 
                  placeholder="What would you like to know?"
                  value={formData.qaQuestion}
                  onChange={(e) => setFormData({ ...formData, qaQuestion: e.target.value })}
                  disabled={loading}
                  maxLength={500}
                />
              </div>
              
              <div>
                <label className="block text-sm font-semibold mb-2">Details (Optional)</label>
                <textarea 
                  className="w-full p-4 bg-gray-100 border-2 border-gray-400 rounded-lg resize-none focus:border-black outline-none transition" 
                  rows="4" 
                  placeholder="Provide additional context or background information..."
                  value={formData.qaDetails}
                  onChange={(e) => setFormData({ ...formData, qaDetails: e.target.value })}
                  disabled={loading}
                  maxLength={2000}
                />
              </div>
              
              <div>
                <label className="block text-sm font-semibold mb-2">Category</label>
                <select 
                  className="w-full p-3 bg-gray-100 border-2 border-gray-400 rounded-lg focus:border-black outline-none transition"
                  value={formData.qaCategory}
                  onChange={(e) => setFormData({ ...formData, qaCategory: e.target.value })}
                  disabled={loading}
                >
                  <option value="">Select a category</option>
                  <option value="general">General</option>
                  <option value="technology">Technology</option>
                  <option value="health">Health & Fitness</option>
                  <option value="education">Education</option>
                  <option value="entertainment">Entertainment</option>
                  <option value="lifestyle">Lifestyle</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div className="flex items-center space-x-2">
                <input 
                  type="checkbox" 
                  id="qa_anonymous" 
                  className="w-4 h-4"
                  checked={formData.qaAnonymous}
                  onChange={(e) => setFormData({ ...formData, qaAnonymous: e.target.checked })}
                  disabled={loading}
                />
                <label htmlFor="qa_anonymous" className="text-sm font-medium">Post anonymously</label>
              </div>
            </div>
          )}

          <div className="p-4 bg-gray-100 border-2 border-gray-400 rounded-lg">
            <label className="block text-sm font-semibold mb-3">Who can see this post?</label>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <label className="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-yellow-100 transition">
                <input 
                  type="radio" 
                  name="privacy" 
                  value="public"
                  checked={formData.privacy === 'public'}
                  onChange={(e) => setFormData({ ...formData, privacy: e.target.value })}
                  disabled={loading}
                  className="w-4 h-4" 
                />
                <span className="text-sm font-medium">Public</span>
              </label>
              <label className="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-yellow-100 transition">
                <input 
                  type="radio" 
                  name="privacy" 
                  value="friends"
                  checked={formData.privacy === 'friends'}
                  onChange={(e) => setFormData({ ...formData, privacy: e.target.value })}
                  disabled={loading}
                  className="w-4 h-4" 
                />
                <span className="text-sm font-medium">Friends Only</span>
              </label>
              <label className="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-yellow-100 transition">
                <input 
                  type="radio" 
                  name="privacy" 
                  value="private"
                  checked={formData.privacy === 'private'}
                  onChange={(e) => setFormData({ ...formData, privacy: e.target.value })}
                  disabled={loading}
                  className="w-4 h-4" 
                />
                <span className="text-sm font-medium">Only Me</span>
              </label>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-3">
            <button 
              onClick={handlePost}
              disabled={loading}
              className={`flex-1 bg-black text-yellow-100 font-medium py-3 rounded-lg hover:bg-gray-800 transition flex items-center justify-center ${
                loading ? 'opacity-50 cursor-not-allowed' : ''
              }`}
            >
              {loading ? (
                <>
                  <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-100 mr-2"></div>
                  Posting...
                </>
              ) : (
                'Post'
              )}
            </button>
            <button 
              onClick={() => navigate('/posts')}
              disabled={loading}
              className={`flex-1 bg-gray-100 border-2 border-gray-400 font-medium py-3 rounded-lg hover:bg-yellow-100 transition ${
                loading ? 'opacity-50 cursor-not-allowed' : ''
              }`}
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default CreatePost;