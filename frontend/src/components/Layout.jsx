import React, { useState, useEffect } from "react";
import { Search, Heart, User, X, Check, AlertCircle, Home, Users, MessageSquare, BookOpen } from "lucide-react";
import { useNavigate, useLocation } from "react-router-dom";

// ===============================
// Notification Component
// ===============================
const Notification = ({ type, message, onClose }) => {
  useEffect(() => {
    const timer = setTimeout(() => onClose(), 5000);
    return () => clearTimeout(timer);
  }, [onClose]);

  const isSuccess = type === "success";

  return (
    <div
      className={`p-4 rounded-lg shadow-md flex items-center justify-between animate-slideIn ${
        isSuccess
          ? "bg-[#EAE4D5] text-black border-l-4 border-black"
          : "bg-red-100 text-black border-l-4 border-red-600"
      }`}
    >
      <div className="flex items-center space-x-3">
        {isSuccess ? (
          <Check className="w-6 h-6" />
        ) : (
          <AlertCircle className="w-6 h-6 text-red-600" />
        )}
        <span className="font-medium">{message}</span>
      </div>
      <button onClick={onClose} className="text-gray-600 hover:text-black">
        <X className="w-5 h-5" />
      </button>
    </div>
  );
};

// ===============================
// Dropdown Component
// ===============================
const Dropdown = ({ trigger, children }) => {
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (isOpen && !e.target.closest(".dropdown-container")) {
        setIsOpen(false);
      }
    };
    document.addEventListener("click", handleClickOutside);
    return () => document.removeEventListener("click", handleClickOutside);
  }, [isOpen]);

  return (
    <div className="relative dropdown-container">
      <div onClick={() => setIsOpen(!isOpen)}>{trigger}</div>
      {isOpen && (
        <div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
          {children}
        </div>
      )}
    </div>
  );
};

// ===============================
// Sidebar Component
// ===============================
const Sidebar = ({ navigateTo }) => {
  const location = useLocation();
  const [openAccordion, setOpenAccordion] = useState(0);

  const isActive = (path) => location.pathname === path;

  const handleAccordion = (value) => {
    setOpenAccordion(openAccordion === value ? 0 : value);
  };

  const MenuItem = ({ icon: Icon, label, path, onClick }) => (
    <button
      onClick={onClick || (() => navigateTo(path))}
      className={`w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors ${
        isActive(path)
          ? "bg-[#EAE4D5] text-black font-medium"
          : "text-gray-700 hover:bg-gray-100"
      }`}
    >
      <Icon className="w-5 h-5" />
      <span>{label}</span>
    </button>
  );

  const AccordionItem = ({ icon: Icon, label, items, accordionId }) => (
    <div>
      <button
        onClick={() => handleAccordion(accordionId)}
        className="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
      >
        <div className="flex items-center space-x-3">
          <Icon className="w-5 h-5" />
          <span>{label}</span>
        </div>
        <svg
          className={`w-4 h-4 transition-transform ${
            openAccordion === accordionId ? "rotate-180" : ""
          }`}
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      {openAccordion === accordionId && (
        <div className="ml-8 mt-1 space-y-1">
          {items.map((item, idx) => (
            <button
              key={idx}
              onClick={() => navigateTo(item.path)}
              className={`w-full text-left px-4 py-2 rounded-lg transition-colors ${
                isActive(item.path)
                  ? "bg-[#EAE4D5] text-black font-medium"
                  : "text-gray-600 hover:bg-gray-100"
              }`}
            >
              {item.label}
            </button>
          ))}
        </div>
      )}
    </div>
  );

  return (
    <div className="w-64 bg-white h-screen shadow-xl border-r border-gray-200 flex flex-col">
      {/* Logo */}
      <div className="p-6 border-b border-gray-200">
        <button
          onClick={() => navigateTo("/")}
          className="flex items-center space-x-3 focus:outline-none"
        >
          <img src="/favicon.png" alt="Rihaab" className="h-8 w-8" />
          <span className="text-xl font-semibold text-gray-800">Rihaab</span>
        </button>
      </div>

      {/* Search */}
      <div className="px-4 py-3 border-b border-gray-200">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            type="text"
            placeholder="Search..."
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
      </div>

      {/* Menu Items */}
      <nav className="flex-1 overflow-y-auto p-4 space-y-2">
        <MenuItem icon={Home} label="Dashboard" path="/" />
        
        <AccordionItem
          icon={BookOpen}
          label="Posts"
          accordionId={1}
          items={[
            { label: "All Posts", path: "/posts" },
            { label: "Create Post", path: "/posts/create" },
          ]}
        />

        <AccordionItem
          icon={MessageSquare}
          label="Stories"
          accordionId={2}
          items={[
            { label: "All Stories", path: "/stories" },
            { label: "Create Story", path: "/stories/create" },
          ]}
        />

        <MenuItem icon={Users} label="Communities" path="/communities" />
        <MenuItem icon={Users} label="People" path="/people" />
        <MenuItem icon={Heart} label="Notifications" path="/notifications" />
        <MenuItem icon={User} label="Profile" path="/profile" />
      </nav>

      {/* Bottom Alert */}
      <div className="p-4 border-t border-gray-200">
        <div className="bg-[#EAE4D5] rounded-lg p-4">
          <div className="flex items-center justify-center mb-2">
            <div className="w-10 h-10 bg-black rounded-lg"></div>
          </div>
          <h3 className="font-semibold text-sm mb-1">Upgrade to PRO</h3>
          <p className="text-xs text-gray-600 mb-3">
            Get access to premium features and more!
          </p>
          <div className="flex space-x-2 text-xs">
            <button className="text-gray-600 hover:text-black">Dismiss</button>
            <button className="text-black font-medium hover:underline">Upgrade Now</button>
          </div>
        </div>
      </div>
    </div>
  );
};

// ===============================
// Navigation Bar (Top)
// ===============================
const Navigation = ({ onSearch }) => {
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const navigate = useNavigate();

  const handleSearch = (e) => {
    e.preventDefault();
    onSearch?.(searchQuery);
    setSearchOpen(false);
  };

  return (
    <nav className="bg-white border-b border-gray-200">
      <div className="px-6">
        <div className="flex justify-end h-16 items-center">
          {/* Right: Icons */}
          <div className="flex items-center space-x-6 text-gray-700">
            {/* Search */}
            <div className="flex items-center">
              <button
                onClick={() => setSearchOpen(!searchOpen)}
                className="mr-2 text-gray-600 hover:text-black focus:outline-none"
              >
                {searchOpen ? <X className="w-5 h-5" /> : <Search className="w-5 h-5" />}
              </button>

              {searchOpen && (
                <form onSubmit={handleSearch} className="overflow-hidden animate-expandWidth">
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search users..."
                    className="border rounded px-3 py-1 w-64 focus:outline-none focus:ring focus:ring-blue-400"
                    autoFocus
                    onBlur={() => setTimeout(() => setSearchOpen(false), 200)}
                  />
                </form>
              )}
            </div>

            {/* Notifications */}
            <button
              onClick={() => navigate("/notifications")}
              className="hover:text-black focus:outline-none"
            >
              <Heart className="w-5 h-5" />
            </button>

            {/* Profile Dropdown */}
            <Dropdown
              trigger={
                <button className="flex items-center focus:outline-none text-gray-700 hover:text-black">
                  <User className="w-5 h-5" />
                </button>
              }
            >
              <button
                onClick={() => navigate("/profile")}
                className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Profile
              </button>
              <button
                onClick={() => navigate("/people")}
                className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                People
              </button>
              <button
                onClick={() => console.log("Logging out...")}
                className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Log Out
              </button>
            </Dropdown>
          </div>
        </div>
      </div>
    </nav>
  );
};

// ===============================
// Layout Wrapper
// ===============================
const Layout = ({ children, header, notification }) => {
  const [currentNotification, setCurrentNotification] = useState(notification);
  const navigate = useNavigate();

  useEffect(() => setCurrentNotification(notification), [notification]);

  const handleSearch = (query) => {
    console.log("Searching for:", query);
    navigate("/people?search=" + encodeURIComponent(query));
  };

  return (
    <div className="flex min-h-screen bg-[#F2F2F2] font-sans antialiased">
      <style>{`
        @keyframes slideIn {
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
        @keyframes expandWidth {
          from { width: 0; opacity: 0; }
          to { width: 16rem; opacity: 1; }
        }
        .animate-slideIn { animation: slideIn 0.5s ease-out; }
        .animate-expandWidth { animation: expandWidth 0.3s ease-out; }
      `}</style>

      {/* Sidebar */}
      <Sidebar navigateTo={navigate} />

      {/* Main Content Area */}
      <div className="flex-1 flex flex-col">
        {/* Top Navbar */}
        <Navigation onSearch={handleSearch} />

        {/* Header */}
        {header && (
          <header className="bg-[#EAE4D5] border-b-2 border-[#B6B09F]">
            <div className="py-6 px-6">{header}</div>
          </header>
        )}

        {/* Notifications */}
        {currentNotification && (
          <div className="px-6 mt-4">
            <Notification
              type={currentNotification.type}
              message={currentNotification.message}
              onClose={() => setCurrentNotification(null)}
            />
          </div>
        )}

        {/* Page Content */}
        <main className="flex-1 overflow-auto">{children}</main>
      </div>
    </div>
  );
};

export default Layout; 