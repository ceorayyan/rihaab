import React, { useState, useRef } from 'react';import Layout from '../../Components/Layout';

const CreateStory = () => {
  const [selectedType, setSelectedType] = useState('');
  const [textContent, setTextContent] = useState('');
  const [imagePreview, setImagePreview] = useState(null);
  const [videoPreview, setVideoPreview] = useState(null);
  const [showPreview, setShowPreview] = useState(false);
  const [caption, setCaption] = useState('');
  const fileInputRef = useRef(null);

  const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (selectedType === 'image') {
          setImagePreview(e.target.result);
          setVideoPreview(null);
        } else if (selectedType === 'video') {
          setVideoPreview(e.target.result);
          setImagePreview(null);
        }
        setShowPreview(true);
      };
      reader.readAsDataURL(file);
    }
  };

  const updateTextPreview = (value) => {
    setTextContent(value);
    setShowPreview(value.trim().length > 0);
    setImagePreview(null);
    setVideoPreview(null);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Handle form submission logic here
    console.log('Form submitted', { selectedType, textContent, caption });
  };

  return (
    <Layout>
      <style>{`
        .story-card {
          background-color: #EAE4D5;
          border: 2px solid #B6B09F;
        }
        
        .type-selector {
          border: 2px solid #B6B09F;
          transition: all 0.3s ease;
        }
        
        .type-selector:hover {
          border-color: #000000;
          background-color: #F2F2F2;
        }
        
        .type-selector.active {
          border-color: #000000;
          background-color: #F2F2F2;
        }
        
        .upload-zone {
          border: 2px dashed #B6B09F;
          background-color: #F2F2F2;
          transition: all 0.3s ease;
        }
        
        .upload-zone:hover {
          border-color: #000000;
          background-color: #EAE4D5;
        }
        
        .form-textarea {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
          color: #000000;
        }
        
        .form-textarea:focus {
          border-color: #000000;
          outline: none;
          box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
          background-color: #000000;
          color: #EAE4D5;
          transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
          background-color: #1a1a1a;
          transform: translateY(-2px);
        }
        
        .btn-secondary {
          background-color: #B6B09F;
          color: #000000;
        }
        
        .btn-secondary:hover {
          background-color: #a09a89;
        }
        
        .info-alert {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
        }
        
        .preview-container {
          background-color: #F2F2F2;
          border: 2px solid #B6B09F;
        }
      `}</style>

      <div className="py-12">
        <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
          <div className="mb-6">
            <h2 className="font-semibold text-xl leading-tight" style={{ color: '#000000' }}>
              Create New Story
            </h2>
          </div>

          <div className="story-card overflow-hidden shadow-md sm:rounded-lg">
            <div className="p-6">
              <form onSubmit={handleSubmit}>
                {/* Story Type Selection */}
                <div className="mb-6">
                  <label className="block text-sm font-medium mb-3" style={{ color: '#000000' }}>
                    Story Type
                  </label>
                  <div className="flex space-x-4">
                    <label className="flex-1">
                      <input
                        type="radio"
                        name="type"
                        value="image"
                        className="sr-only"
                        checked={selectedType === 'image'}
                        onChange={(e) => setSelectedType(e.target.value)}
                      />
                      <div
                        className={`type-selector rounded-lg p-4 cursor-pointer text-center ${
                          selectedType === 'image' ? 'active' : ''
                        }`}
                      >
                        <svg
                          className="w-8 h-8 mx-auto mb-2"
                          style={{ color: '#000000' }}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                        </svg>
                        <span className="font-medium" style={{ color: '#000000' }}>
                          Image
                        </span>
                      </div>
                    </label>

                    <label className="flex-1">
                      <input
                        type="radio"
                        name="type"
                        value="video"
                        className="sr-only"
                        checked={selectedType === 'video'}
                        onChange={(e) => setSelectedType(e.target.value)}
                      />
                      <div
                        className={`type-selector rounded-lg p-4 cursor-pointer text-center ${
                          selectedType === 'video' ? 'active' : ''
                        }`}
                      >
                        <svg
                          className="w-8 h-8 mx-auto mb-2"
                          style={{ color: '#000000' }}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                        </svg>
                        <span className="font-medium" style={{ color: '#000000' }}>
                          Video
                        </span>
                      </div>
                    </label>

                    <label className="flex-1">
                      <input
                        type="radio"
                        name="type"
                        value="text"
                        className="sr-only"
                        checked={selectedType === 'text'}
                        onChange={(e) => setSelectedType(e.target.value)}
                      />
                      <div
                        className={`type-selector rounded-lg p-4 cursor-pointer text-center ${
                          selectedType === 'text' ? 'active' : ''
                        }`}
                      >
                        <svg
                          className="w-8 h-8 mx-auto mb-2"
                          style={{ color: '#000000' }}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" />
                        </svg>
                        <span className="font-medium" style={{ color: '#000000' }}>
                          Text
                        </span>
                      </div>
                    </label>
                  </div>
                </div>

                {/* File Upload */}
                {(selectedType === 'image' || selectedType === 'video') && (
                  <div className="mb-6">
                    <label className="block text-sm font-medium mb-2" style={{ color: '#000000' }}>
                      Upload File
                    </label>
                    <div className="mt-1 flex justify-center px-6 pt-5 pb-6 upload-zone rounded-md">
                      <div className="space-y-1 text-center">
                        <svg
                          className="mx-auto h-12 w-12"
                          style={{ color: '#B6B09F' }}
                          stroke="currentColor"
                          fill="none"
                          viewBox="0 0 48 48"
                        >
                          <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                          />
                        </svg>
                        <div className="flex text-sm" style={{ color: '#000000' }}>
                          <label className="relative cursor-pointer rounded-md font-medium hover:underline">
                            <span>Upload a file</span>
                            <input
                              type="file"
                              name="file"
                              className="sr-only"
                              ref={fileInputRef}
                              onChange={handleFileSelect}
                              accept={selectedType === 'image' ? 'image/*' : 'video/*'}
                            />
                          </label>
                          <p className="pl-1">or drag and drop</p>
                        </div>
                        <p className="text-xs" style={{ color: '#B6B09F' }}>
                          {selectedType === 'image'
                            ? 'PNG, JPG, GIF up to 10MB'
                            : 'MP4, WebM up to 10MB'}
                        </p>
                      </div>
                    </div>
                  </div>
                )}

                {/* Text Content */}
                {selectedType === 'text' && (
                  <div className="mb-6">
                    <label htmlFor="content" className="block text-sm font-medium mb-2" style={{ color: '#000000' }}>
                      Story Content
                    </label>
                    <textarea
                      name="content"
                      id="content"
                      rows="6"
                      className="form-textarea block w-full rounded-md shadow-sm"
                      placeholder="Write your story here..."
                      value={textContent}
                      onChange={(e) => updateTextPreview(e.target.value)}
                    />
                  </div>
                )}

                {/* Caption */}
                <div className="mb-6">
                  <label htmlFor="caption" className="block text-sm font-medium mb-2" style={{ color: '#000000' }}>
                    Caption (Optional)
                  </label>
                  <textarea
                    name="caption"
                    id="caption"
                    rows="2"
                    className="form-textarea block w-full rounded-md shadow-sm"
                    placeholder="Add a caption to your story..."
                    maxLength="500"
                    value={caption}
                    onChange={(e) => setCaption(e.target.value)}
                  />
                  <p className="mt-1 text-sm" style={{ color: '#B6B09F' }}>
                    Maximum 500 characters
                  </p>
                </div>

                {/* Preview */}
                {showPreview && (
                  <div className="mb-6">
                    <label className="block text-sm font-medium mb-2" style={{ color: '#000000' }}>
                      Preview
                    </label>
                    <div className="preview-container rounded-lg p-4">
                      {selectedType === 'image' && imagePreview && (
                        <div>
                          <img src={imagePreview} className="max-h-64 mx-auto rounded" alt="Preview" />
                        </div>
                      )}
                      {selectedType === 'video' && videoPreview && (
                        <div>
                          <video controls className="max-h-64 mx-auto rounded">
                            <source src={videoPreview} />
                          </video>
                        </div>
                      )}
                      {selectedType === 'text' && textContent && (
                        <div className="text-center">
                          <p style={{ color: '#000000' }}>{textContent}</p>
                        </div>
                      )}
                    </div>
                  </div>
                )}

                {/* Info Alert */}
                <div className="mb-6 p-4 info-alert rounded-lg">
                  <div className="flex">
                    <svg
                      className="flex-shrink-0 w-5 h-5"
                      style={{ color: '#000000' }}
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fillRule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clipRule="evenodd"
                      />
                    </svg>
                    <div className="ml-3">
                      <p className="text-sm" style={{ color: '#000000' }}>
                        Your story will be visible for 24 hours and only to users who have accepted key requests with you.
                      </p>
                    </div>
                  </div>
                </div>

                {/* Form Actions */}
                <div className="flex justify-between">
                  <button
                    type="button"
                    onClick={() => window.history.back()}
                    className="btn-secondary font-bold py-2 px-4 rounded"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="btn-primary font-bold py-2 px-4 rounded">
                    Share Story
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default CreateStory;