import React, { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  Home,
  FileText,
  Layers,
  Users,
  Bell,
  User,
  ChevronDown,
  ChevronUp,
} from "lucide-react";

const Sidebar = () => {
  const location = useLocation();
  const [openDropdown, setOpenDropdown] = useState(null);

  const toggleDropdown = (label) => {
    setOpenDropdown(openDropdown === label ? null : label);
  };

  const menu = [
    { icon: Home, label: "Dashboard", path: "/" },
    {
      icon: FileText,
      label: "Posts",
      subItems: [
        { label: "All Posts", path: "/posts" },
        { label: "Create Post", path: "/posts/create" },
      ],
    },
    {
      icon: Layers,
      label: "Stories",
      subItems: [
        { label: "All Stories", path: "/stories" },
        { label: "Create Story", path: "/stories/create" },
      ],
    },
    {
      icon: Users,
      label: "Communities",
      subItems: [
        { label: "All Communities", path: "/communities" },
        { label: "Create Community", path: "/communities/create" },
      ],
    },
    {
      icon: User,
      label: "Profile",
      subItems: [
        { label: "My Profile", path: "/profile" },
        { label: "Edit Profile", path: "/profile/edit" },
        { label: "My Feed", path: "/profile/feed" },
      ],
    },
    { icon: Bell, label: "Notifications", path: "/notifications" },
    { icon: Users, label: "People", path: "/people" },
  ];

  return (
    <aside
      className="w-64 min-h-screen flex flex-col justify-between shadow-md"
      style={{ backgroundColor: "#EAE4D5" }}
    >
      <div>
        {/* Brand */}
        <div
          className="p-6 text-2xl font-bold tracking-wide select-none"
          style={{ color: "#000000" }}
        >
          Rihaab
        </div>

        {/* Navigation */}
        <nav className="px-3 space-y-1">
          {menu.map((item, i) => {
            const isActive = location.pathname === item.path;
            const hasSubItems = item.subItems && item.subItems.length > 0;
            const isDropdownOpen = openDropdown === item.label;

            return (
              <div key={i}>
                {/* Main Item */}
                <button
                  onClick={() =>
                    hasSubItems ? toggleDropdown(item.label) : null
                  }
                  className={`w-full flex items-center justify-between px-4 py-2 rounded-lg transition-all duration-200
                    ${
                      isActive
                        ? "font-semibold"
                        : "hover:bg-[#F2F2F2] hover:text-[#000000]"
                    }`}
                  style={{
                    color: "#000000",
                    backgroundColor: isActive ? "#B6B09F" : "transparent",
                  }}
                >
                  <div className="flex items-center">
                    <item.icon className="w-5 h-5 mr-3" />
                    {item.label}
                  </div>
                  {hasSubItems &&
                    (isDropdownOpen ? (
                      <ChevronUp className="w-4 h-4" />
                    ) : (
                      <ChevronDown className="w-4 h-4" />
                    ))}
                </button>

                {/* Sub Items */}
                {hasSubItems && (
                  <div
                    className={`overflow-hidden transition-all duration-300 ml-10 ${
                      isDropdownOpen ? "max-h-40" : "max-h-0"
                    }`}
                  >
                    {item.subItems.map((sub, j) => {
                      const isSubActive = location.pathname === sub.path;
                      return (
                        <Link
                          key={j}
                          to={sub.path}
                          className={`block py-1.5 text-sm rounded-md transition
                            ${
                              isSubActive
                                ? "font-semibold text-[#000000]"
                                : "text-[#333] hover:text-[#000000]"
                            }`}
                        >
                          {sub.label}
                        </Link>
                      );
                    })}
                  </div>
                )}
              </div>
            );
          })}
        </nav>
      </div>

      {/* Upgrade Card */}
      <div
        className="m-4 p-4 rounded-xl shadow-sm"
        style={{ backgroundColor: "#F2F2F2", color: "#000000" }}
      >
        <h3 className="text-sm font-semibold mb-1">Upgrade to PRO</h3>
        <p className="text-xs mb-3" style={{ color: "#333" }}>
          Get access to premium features and more!
        </p>
        <button
          className="w-full py-2 text-sm rounded-lg transition"
          style={{
            backgroundColor: "#000000",
            color: "#F2F2F2",
          }}
        >
          Upgrade Now
        </button>
      </div>
    </aside>
  );
};

export default Sidebar;
