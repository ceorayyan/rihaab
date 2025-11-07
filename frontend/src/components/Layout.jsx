import React from "react";
import Sidebar from "./layout/sidebar";
import Navigation from "./layout/navigation";

const Layout = ({ children, header }) => {
  return (
    <div className="flex h-screen bg-gray-50">
      {/* Sidebar */}
      <Sidebar />

      {/* Main Content */}
      <div className="flex-1 flex flex-col">
        {/* Top Navigation */}
        <Navigation header={header} />

        {/* Page Body */}
        <main className="flex-1 overflow-y-auto p-6">{children}</main>
      </div>
    </div>
  );
};

export default Layout;
