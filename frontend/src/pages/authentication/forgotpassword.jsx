import React, { useState } from 'react';
import { Link } from 'react-router-dom';

const ForgotPassword = () => {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    // Handle forgot password logic here
    console.log('Reset email for:', email);
    setStatus('We have emailed your password reset link!');
  };

  return (
    <div className="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style={{ backgroundColor: '#EAE4D5' }}>
      <style>{`
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes slideRight {
          from {
            width: 0%;
          }
          to {
            width: 100%;
          }
        }

        .forgot-container {
          animation: fadeInUp 0.8s ease-out;
          position: relative;
          overflow: hidden;
        }

        .forgot-container::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 3px;
          background-color: #000000;
          animation: slideRight 1s ease-out;
        }

        .form-input {
          transition: all 0.3s ease;
          border: 2px solid #B6B09F;
          border-radius: 8px;
          padding: 0.875rem 1rem;
          font-size: 0.95rem;
          background-color: #F2F2F2;
          color: #000000;
        }

        .form-input::placeholder {
          color: #B6B09F;
        }

        .form-input:focus {
          border-color: #000000;
          background-color: #ffffff;
          box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
          transform: translateY(-2px);
          outline: none;
        }

        .form-label {
          font-weight: 600;
          color: #000000;
          margin-bottom: 0.5rem;
          font-size: 0.875rem;
          letter-spacing: 0.3px;
        }

        .submit-button {
          background-color: #000000;
          color: #EAE4D5;
          padding: 0.875rem 2rem;
          border-radius: 8px;
          font-weight: 600;
          transition: all 0.3s ease;
          border: none;
          cursor: pointer;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .submit-button:hover {
          background-color: #1a1a1a;
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .submit-button:active {
          transform: translateY(0);
        }

        .back-link {
          color: #000000;
          font-size: 0.875rem;
          transition: all 0.3s ease;
          text-decoration: none;
          border-bottom: 1px solid transparent;
        }

        .back-link:hover {
          color: #000000;
          border-bottom: 1px solid #000000;
        }

        .title-text {
          font-size: 2rem;
          font-weight: 800;
          color: #000000;
          margin-bottom: 0.5rem;
          text-align: center;
          letter-spacing: -0.5px;
        }

        .subtitle-text {
          color: #B6B09F;
          text-align: center;
          margin-bottom: 2rem;
          font-size: 0.875rem;
          line-height: 1.6;
        }

        .status-message {
          background-color: #EAE4D5;
          border: 2px solid #000000;
          border-radius: 8px;
          padding: 0.875rem;
          margin-bottom: 1.5rem;
          color: #000000;
          font-size: 0.875rem;
          animation: fadeInUp 0.5s ease-out;
        }
      `}</style>

      <div className="max-w-md w-full space-y-8 p-8 rounded-lg" style={{ backgroundColor: '#ffffff' }}>
        <div className="forgot-container">
          <h1 className="title-text">Forgot Password?</h1>
          <p className="subtitle-text">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
          </p>

          {status && (
            <div className="status-message">
              {status}
            </div>
          )}

          <form onSubmit={handleSubmit}>
            {/* Email Address */}
            <div className="mb-6" style={{ animation: 'fadeInUp 0.8s ease-out 0.1s backwards' }}>
              <label htmlFor="email" className="form-label">
                Email Address
              </label>
              <input
                id="email"
                className="form-input block w-full"
                type="email"
                name="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                autoFocus
                placeholder="Enter your email"
              />
            </div>

            <button
              type="submit"
              className="submit-button w-full"
              style={{ animation: 'fadeInUp 0.8s ease-out 0.2s backwards' }}
            >
              Email Password Reset Link
            </button>

            <div
              style={{
                textAlign: 'center',
                marginTop: '1.5rem',
                animation: 'fadeInUp 0.8s ease-out 0.3s backwards',
              }}
            >
              <Link to="/login" className="back-link font-semibold">
                ← Back to Sign In
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default ForgotPassword;