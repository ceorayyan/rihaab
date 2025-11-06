import React, { useState } from "react";
import { Routes, Route, Link } from "react-router-dom";
import Layout from "./components/Layout";

// ✅ Pages
import Profile from "./pages/Profile";
import Notifications from "./pages/Notifications";
import People from "./pages/People";

// ✅ Posts Pages
import PostsIndex from "./pages/posts/index";
import CreatePost from "./pages/posts/create";

// ✅ Stories Pages
import StoriesIndex from "./pages/stories/index";
import CreateStory from "./pages/stories/create";
import ShowStory from "./pages/stories/show";

// ✅ Authentication Pages
import Login from "./pages/authentication/Login";
import Register from "./pages/authentication/Register";
import ForgotPassword from "./pages/authentication/ForgotPassword";

// ✅ Community Pages
import CommunityIndex from "./pages/community/index";
import CommunityCreate from "./pages/community/create";
import CommunityShow from "./pages/community/show";
import CommunityChannel from "./pages/community/channel";

// ✅ Profile Pages
import ProfileEdit from "./pages/profile/edit";
import Feed from "./pages/profile/feed";
import UserFeed from "./pages/profile/user_feed";
import PublicProfile from "./pages/profile/public";

export default function App() {
  const [notification, setNotification] = useState({
    type: "success",
    message: "Welcome to your React application!",
  });

  return (
    <Routes>
      {/* ✅ Dashboard (Main Page with Layout + Navbar) */}
      <Route
        path="/"
        element={
          <Layout
            header={
              <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
              </h2>
            }
            notification={notification}
          >
            <div className="p-6 text-gray-900">
              <h3 className="text-2xl font-bold mb-4">Welcome to Dashboard</h3>
              <p className="mb-4">Navigate using the links below:</p>

              <div className="space-x-3 mb-6">
                <Link to="/profile" className="btn">Profile</Link>
                <Link to="/notifications" className="btn">Notifications</Link>
                <Link to="/people" className="btn">People</Link>
                <Link to="/posts" className="btn">Posts</Link>
                <Link to="/stories" className="btn">Stories</Link>
                <Link to="/communities" className="btn">Communities</Link>
              </div>

              <div className="space-x-2">
                <button
                  onClick={() =>
                    setNotification({
                      type: "success",
                      message: "Action completed successfully!",
                    })
                  }
                  className="btn bg-green-100 hover:bg-green-200"
                >
                  Show Success
                </button>
                <button
                  onClick={() =>
                    setNotification({
                      type: "error",
                      message: "An error occurred!",
                    })
                  }
                  className="btn bg-red-100 hover:bg-red-200"
                >
                  Show Error
                </button>
                <button
                  onClick={() => setNotification(null)}
                  className="btn bg-gray-200 hover:bg-gray-300"
                >
                  Clear Notification
                </button>
              </div>
            </div>
          </Layout>
        }
      />

      {/* ✅ Authentication Routes */}
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/forgot-password" element={<ForgotPassword />} />

      {/* ✅ Other Pages */}
      <Route path="/profile" element={<Profile />} />
      <Route path="/notifications" element={<Notifications />} />
      <Route path="/people" element={<People />} />

      {/* ✅ Profile Routes */}
      <Route path="/profile/edit" element={<ProfileEdit />} />
      <Route path="/profile/feed" element={<Feed />} />
      <Route path="/profile/:username" element={<PublicProfile />} />
      <Route path="/profile/:username/feed" element={<UserFeed />} />

      {/* ✅ Posts Routes */}
      <Route path="/posts" element={<PostsIndex />} />
      <Route path="/posts/create" element={<CreatePost />} />

      {/* ✅ Stories Routes */}
      <Route path="/stories" element={<StoriesIndex />} />
      <Route path="/stories/create" element={<CreateStory />} />
      <Route path="/stories/:id" element={<ShowStory />} />

      {/* ✅ Community Routes */}
      <Route path="/communities" element={<CommunityIndex />} />
      <Route path="/communities/create" element={<CommunityCreate />} />
      <Route path="/communities/:slug" element={<CommunityShow />} />
      <Route path="/communities/:slug/channels/:channelSlug" element={<CommunityChannel />} />
    </Routes>
  );
}