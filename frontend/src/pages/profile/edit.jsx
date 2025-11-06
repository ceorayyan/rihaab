import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Layout from '../../Components/Layout';

const ProfileEdit = () => {
  const navigate = useNavigate();
  
  // Mock user data - replace with actual API data
  const [user] = useState({
    name: 'John Doe',
    email: 'john@example.com',
    profile_picture: null,
    bio: '',
    dob: '',
    marital_status: '',
    education: '',
    occupation: '',
  });

  const [formData, setFormData] = useState({
    bio: user.bio || '',
    dob: user.dob || '',
    marital_status: user.marital_status || '',
    education: user.education || '',
    occupation: user.occupation || '',
  });

  const [profilePreview, setProfilePreview] = useState(
    user.profile_picture || 'https://via.placeholder.com/100'
  );

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleProfilePictureChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (ev) => {
        setProfilePreview(ev.target.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log('Form submitted:', formData);
    // Handle form submission logic here
    navigate('/profile');
  };

  return (
    <Layout>
      <style>{`
        @keyframes slideDown {
          from { opacity: 0; transform: translateY(-20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down {
          animation: slideDown 0.5s ease-out;
        }
      `}</style>

      <div className="max-w-3xl mx-auto py-8" style={{ backgroundColor: '#F2F2F2', minHeight: '100vh' }}>
        <div className="shadow rounded-xl p-6 animate-slide-down" style={{ backgroundColor: '#EAE4D5', border: '2px solid #B6B09F' }}>
          <h2 className="text-lg font-semibold mb-6" style={{ color: '#000000' }}>
            Update Your Profile
          </h2>

          {/* Profile Update Form */}
          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Profile Picture */}
            <div className="flex items-center space-x-4">
              <div className="relative">
                <img
                  id="profilePreview"
                  src={profilePreview}
                  className="w-24 h-24 rounded-full object-cover shadow-sm transition-all duration-300 hover:scale-110"
                  style={{ border: '3px solid #B6B09F' }}
                  alt="Profile"
                />
                <label
                  htmlFor="profile_picture"
                  className="absolute bottom-0 right-0 p-1.5 rounded-full shadow cursor-pointer transition-all duration-300 hover:scale-110"
                  style={{ backgroundColor: '#000000' }}
                >
                  <svg className="w-5 h-5" style={{ color: '#F2F2F2' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                  </svg>
                </label>
              </div>
              <input
                type="file"
                id="profile_picture"
                name="profile_picture"
                className="hidden"
                accept="image/*"
                onChange={handleProfilePictureChange}
              />
              <div>
                <p className="font-medium" style={{ color: '#000000' }}>
                  {user.name}
                </p>
                <p className="text-sm" style={{ color: '#B6B09F' }}>
                  {user.email}
                </p>
              </div>
            </div>

            {/* Bio */}
            <div>
              <label htmlFor="bio" className="block text-sm font-medium mb-1" style={{ color: '#000000' }}>
                Bio
              </label>
              <textarea
                id="bio"
                name="bio"
                rows="2"
                value={formData.bio}
                onChange={handleChange}
                className="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                placeholder="Tell us about yourself..."
              />
            </div>

            {/* DOB + Marital Status */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label htmlFor="dob" className="block text-sm font-medium mb-1" style={{ color: '#000000' }}>
                  Date of Birth
                </label>
                <input
                  type="date"
                  id="dob"
                  name="dob"
                  value={formData.dob}
                  onChange={handleChange}
                  className="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                  style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                />
              </div>
              <div>
                <label htmlFor="marital_status" className="block text-sm font-medium mb-1" style={{ color: '#000000' }}>
                  Marital Status
                </label>
                <select
                  id="marital_status"
                  name="marital_status"
                  value={formData.marital_status}
                  onChange={handleChange}
                  className="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                  style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
                >
                  <option value="">Select</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Divorced">Divorced</option>
                  <option value="Widowed">Widowed</option>
                </select>
              </div>
            </div>

            {/* Education */}
            <div>
              <label htmlFor="education" className="block text-sm font-medium mb-1" style={{ color: '#000000' }}>
                Education
              </label>
              <input
                type="text"
                id="education"
                name="education"
                value={formData.education}
                onChange={handleChange}
                placeholder="e.g., Bachelor's in CS"
                className="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
              />
            </div>

            {/* Occupation */}
            <div>
              <label htmlFor="occupation" className="block text-sm font-medium mb-1" style={{ color: '#000000' }}>
                Occupation
              </label>
              <input
                type="text"
                id="occupation"
                name="occupation"
                value={formData.occupation}
                onChange={handleChange}
                placeholder="e.g., Software Engineer"
                className="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                style={{ border: '2px solid #B6B09F', backgroundColor: '#F2F2F2', color: '#000000' }}
              />
            </div>

            {/* Save Button */}
            <div className="flex justify-end pt-4">
              <button
                type="button"
                onClick={() => navigate('/profile')}
                className="px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg mr-2"
                style={{ backgroundColor: '#B6B09F', color: '#000000' }}
              >
                Cancel
              </button>
              <button
                type="submit"
                className="px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                style={{ backgroundColor: '#000000', color: '#F2F2F2' }}
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  );
};

export default ProfileEdit;