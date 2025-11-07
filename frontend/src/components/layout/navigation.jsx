import React, { useState } from "react";
import { Bell, Search, User } from "lucide-react";
import { useNavigate, Link } from "react-router-dom";
import Dropdown from "./dropdown";

const Navigation = ({ header }) => {
  const [searchTerm, setSearchTerm] = useState("");
  const navigate = useNavigate();

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchTerm.trim()) {
      navigate(`/people?search=${encodeURIComponent(searchTerm.trim())}`);
    } else {
      navigate("/people");
    }
  };

  return (
    <header
      className="flex items-center justify-between px-6 py-3 sticky top-0 z-50 shadow-sm border-b"
      style={{ backgroundColor: "#EAE4D5", borderColor: "#B6B09F" }}
    >
      {/* Left: Header title */}
      <h1
        className="text-xl font-semibold tracking-wide"
        style={{ color: "#000000" }}
      >
        {header || "Rihaab"}
      </h1>

      {/* Center: Search bar */}
      <form
        onSubmit={handleSearch}
        className="hidden md:flex items-center rounded-full px-3 py-2 w-1/3 transition-all duration-200"
        style={{ backgroundColor: "#F2F2F2" }}
      >
        <Search className="w-4 h-4 mr-2" style={{ color: "#B6B09F" }} />
        <input
          type="text"
          placeholder="Search people..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="bg-transparent outline-none flex-1 text-sm"
          style={{ color: "#000000" }}
        />
      </form>

      {/* Right: Icons and User Menu */}
      <div className="flex items-center space-x-6">
        {/* Notifications */}
        <Link
          to="/notifications"
          className="relative group"
        >
          <Bell
            className="w-6 h-6 transition-colors duration-200"
            style={{ color: "#000000" }}
          />
          <span
            className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 hidden group-hover:block"
          >
            3
          </span>
        </Link>

        {/* User Menu */}
        <Dropdown
          trigger={
            <div className="flex items-center space-x-2 cursor-pointer">
              <User className="w-6 h-6" style={{ color: "#000000" }} />
              <span
                className="font-medium hidden sm:block"
                style={{ color: "#000000" }}
              >
                Humail
              </span>
            </div>
          }
        >
          <Link
            to="/profile"
            className="block px-4 py-2 text-sm hover:bg-[#F2F2F2] transition"
            style={{ color: "#000000" }}
          >
            Profile
          </Link>
          <Link
            to="/settings"
            className="block px-4 py-2 text-sm hover:bg-[#F2F2F2] transition"
            style={{ color: "#000000" }}
          >
            Settings
          </Link>
          <button
            onClick={() => {
              localStorage.clear();
              navigate("/login");
            }}
            className="w-full text-left block px-4 py-2 text-sm hover:bg-[#F2F2F2] transition"
            style={{ color: "#B00020" }}
          >
            Logout
          </button>
        </Dropdown>
      </div>
    </header>
  );
};

export default Navigation;
