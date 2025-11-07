import React, { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import Layout from "../../Components/Layout";
import axios from "axios";

const CommunityCreate = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: "",
    description: "",
    is_private: false,
  });
  const [icon, setIcon] = useState(null);
  const [banner, setBanner] = useState(null);
  const [iconPreview, setIconPreview] = useState(null);
  const [bannerPreview, setBannerPreview] = useState(null);
  const [loading, setLoading] = useState(false);

  // Handle text inputs and checkbox
  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === "checkbox" ? checked : value,
    }));
  };

  // Handle icon upload + preview
  const handleIconChange = (e) => {
    const file = e.target.files[0];
    setIcon(file);
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => setIconPreview(event.target.result);
      reader.readAsDataURL(file);
    }
  };

  // Handle banner upload + preview
  const handleBannerChange = (e) => {
    const file = e.target.files[0];
    setBanner(file);
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => setBannerPreview(event.target.result);
      reader.readAsDataURL(file);
    }
  };

  // Handle submit and integrate with Laravel API
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const data = new FormData();
      data.append("name", formData.name);
      data.append("description", formData.description);
      data.append("is_private", formData.is_private ? 1 : 0);
      if (icon) data.append("icon", icon);
      if (banner) data.append("banner", banner);

      const token = localStorage.getItem("token");
      const res = await axios.post("http://127.0.0.1:8000/api/communities", data, {
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
      });

      alert("✅ Community created successfully!");
      navigate("/communities");
    } catch (err) {
      console.error(err);
      alert(err.response?.data?.message || "❌ Failed to create community.");
    } finally {
      setLoading(false);
    }
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
        .preview-image {
          transition: all 0.3s ease;
        }
      `}</style>

      <div
        className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
        style={{ backgroundColor: "#F2F2F2", minHeight: "100vh" }}
      >
        {/* Header */}
        <div className="mb-6 animate-fade-in">
          <Link
            to="/communities"
            className="inline-flex items-center text-sm mb-4 transition-colors duration-300 hover:underline"
            style={{ color: "#B6B09F" }}
          >
            <svg
              className="w-4 h-4 mr-1"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fillRule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clipRule="evenodd"
              />
            </svg>
            Back to Communities
          </Link>
          <h1 className="text-3xl font-bold" style={{ color: "#000000" }}>
            Create Community
          </h1>
          <p className="text-sm mt-1" style={{ color: "#B6B09F" }}>
            Build your own community and connect with people
          </p>
        </div>

        {/* Form */}
        <div
          className="rounded-lg p-6 animate-fade-in"
          style={{
            backgroundColor: "#FFFFFF",
            border: "1px solid #B6B09F",
          }}
        >
          <form onSubmit={handleSubmit}>
            {/* Community Name */}
            <div className="mb-6">
              <label
                htmlFor="name"
                className="block text-sm font-semibold mb-2"
                style={{ color: "#000000" }}
              >
                Community Name <span style={{ color: "#EF4444" }}>*</span>
              </label>
              <input
                type="text"
                name="name"
                id="name"
                value={formData.name}
                onChange={handleChange}
                required
                className="w-full px-4 py-3 rounded-lg transition-all duration-300 focus:ring-2 focus:outline-none"
                style={{
                  border: "2px solid #B6B09F",
                  backgroundColor: "#F2F2F2",
                  color: "#000000",
                }}
                placeholder="e.g., Photography Enthusiasts"
              />
            </div>

            {/* Description */}
            <div className="mb-6">
              <label
                htmlFor="description"
                className="block text-sm font-semibold mb-2"
                style={{ color: "#000000" }}
              >
                Description
              </label>
              <textarea
                name="description"
                id="description"
                rows="4"
                value={formData.description}
                onChange={handleChange}
                className="w-full px-4 py-3 rounded-lg transition-all duration-300 focus:ring-2 focus:outline-none"
                style={{
                  border: "2px solid #B6B09F",
                  backgroundColor: "#F2F2F2",
                  color: "#000000",
                }}
                placeholder="Tell people what your community is about..."
              />
            </div>

            {/* Icon Upload */}
            <div className="mb-6">
              <label
                htmlFor="icon"
                className="block text-sm font-semibold mb-2"
                style={{ color: "#000000" }}
              >
                Community Icon
              </label>
              <div className="flex items-center space-x-4">
                <div
                  id="iconPreview"
                  className="w-24 h-24 rounded-lg overflow-hidden flex items-center justify-center"
                  style={{
                    backgroundColor: "#EAE4D5",
                    border: "2px solid #B6B09F",
                  }}
                >
                  {iconPreview ? (
                    <img
                      src={iconPreview}
                      className="w-full h-full object-cover preview-image"
                      alt="Icon preview"
                    />
                  ) : (
                    <svg
                      className="w-12 h-12"
                      style={{ color: "#B6B09F" }}
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  )}
                </div>
                <div className="flex-1">
                  <input
                    type="file"
                    name="icon"
                    id="icon"
                    accept="image/*"
                    className="hidden"
                    onChange={handleIconChange}
                  />
                  <label
                    htmlFor="icon"
                    className="inline-block px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300 hover:translate-y-[-2px]"
                    style={{
                      backgroundColor: "#EAE4D5",
                      color: "#000000",
                      border: "1px solid #B6B09F",
                    }}
                  >
                    Choose Icon
                  </label>
                  <p className="text-xs mt-2" style={{ color: "#B6B09F" }}>
                    Recommended: Square image, max 2MB
                  </p>
                </div>
              </div>
            </div>

            {/* Banner Upload */}
            <div className="mb-6">
              <label
                htmlFor="banner"
                className="block text-sm font-semibold mb-2"
                style={{ color: "#000000" }}
              >
                Community Banner
              </label>
              <div className="space-y-3">
                <div
                  id="bannerPreview"
                  className="w-full h-32 rounded-lg overflow-hidden flex items-center justify-center"
                  style={{
                    background: bannerPreview
                      ? "none"
                      : "linear-gradient(135deg, #000000 0%, #B6B09F 100%)",
                    border: "2px solid #B6B09F",
                  }}
                >
                  {bannerPreview ? (
                    <img
                      src={bannerPreview}
                      className="w-full h-full object-cover preview-image"
                      alt="Banner preview"
                    />
                  ) : (
                    <svg
                      className="w-12 h-12"
                      style={{ color: "#F2F2F2" }}
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  )}
                </div>
                <input
                  type="file"
                  name="banner"
                  id="banner"
                  accept="image/*"
                  className="hidden"
                  onChange={handleBannerChange}
                />
                <label
                  htmlFor="banner"
                  className="inline-block px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300 hover:translate-y-[-2px]"
                  style={{
                    backgroundColor: "#EAE4D5",
                    color: "#000000",
                    border: "1px solid #B6B09F",
                  }}
                >
                  Choose Banner
                </label>
                <p className="text-xs" style={{ color: "#B6B09F" }}>
                  Recommended: 1200x300px, max 5MB
                </p>
              </div>
            </div>

            {/* Privacy Setting */}
            <div className="mb-6">
              <label className="flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  name="is_private"
                  checked={formData.is_private}
                  onChange={handleChange}
                  className="w-5 h-5 rounded transition-all duration-300"
                  style={{ border: "2px solid #B6B09F", color: "#000000" }}
                />
                <span className="ml-3">
                  <span
                    className="block font-semibold"
                    style={{ color: "#000000" }}
                  >
                    Private Community
                  </span>
                  <span className="text-sm" style={{ color: "#B6B09F" }}>
                    Only approved members can join and view content
                  </span>
                </span>
              </label>
            </div>

            {/* Submit Button */}
            <div
              className="flex items-center justify-end space-x-3 pt-4"
              style={{ borderTop: "2px solid #B6B09F" }}
            >
              <Link
                to="/communities"
                className="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                style={{
                  backgroundColor: "#EAE4D5",
                  color: "#000000",
                  border: "1px solid #B6B09F",
                }}
              >
                Cancel
              </Link>
              <button
                type="submit"
                disabled={loading}
                className="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl disabled:opacity-60"
                style={{
                  backgroundColor: "#000000",
                  color: "#F2F2F2",
                }}
              >
                {loading ? "Creating..." : "Create Community"}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  );
};

export default CommunityCreate;
