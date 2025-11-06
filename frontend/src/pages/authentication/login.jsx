import React, { useState } from 'react';
import { API_BASE_URL } from '../../config';
import { Link, useNavigate } from 'react-router-dom';

const Login = () => {
  const navigate = useNavigate();
  
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    remember: false,
  });

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  // Your Laravel API URL
  const API_BASE_URL = 'http://localhost:8000/api';

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
    // Clear error when user starts typing
    if (error) setError('');
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const response = await fetch(`${API_BASE_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        // Handle validation errors
        if (data.errors) {
          const errorMessages = Object.values(data.errors).flat().join(', ');
          throw new Error(errorMessages);
        }
        throw new Error(data.message || 'Login failed. Please try again.');
      }

      if (data.success) {
        // Store authentication token
        localStorage.setItem('auth_token', data.token);
        
        // Store user data
        localStorage.setItem('user', JSON.stringify(data.user));

        // Optional: Handle remember me
        if (formData.remember) {
          localStorage.setItem('remember_user', 'true');
        }

        console.log('Login successful:', data);

        // Redirect to profile or home page
        navigate('/profile'); // Change this to your desired route
      } else {
        throw new Error(data.message || 'Login failed');
      }
    } catch (err) {
      console.error('Login error:', err);
      setError(err.message || 'An error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
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

        @keyframes shake {
          0%, 100% { transform: translateX(0); }
          25% { transform: translateX(-10px); }
          75% { transform: translateX(10px); }
        }

        .login-container {
          animation: fadeInUp 0.8s ease-out;
          position: relative;
          overflow: hidden;
        }

        .login-container::before {
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

        .form-input.error {
          border-color: #dc2626;
          animation: shake 0.4s ease-in-out;
        }

        .form-label {
          font-weight: 600;
          color: #000000;
          margin-bottom: 0.5rem;
          font-size: 0.875rem;
          letter-spacing: 0.3px;
        }

        .login-button {
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

        .login-button:hover:not(:disabled) {
          background-color: #1a1a1a;
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .login-button:active:not(:disabled) {
          transform: translateY(0);
        }

        .login-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        .forgot-link {
          color: #000000;
          font-size: 0.875rem;
          transition: all 0.3s ease;
          text-decoration: none;
          border-bottom: 1px solid transparent;
        }

        .forgot-link:hover {
          color: #000000;
          border-bottom: 1px solid #000000;
        }

        .checkbox-custom {
          width: 1.125rem;
          height: 1.125rem;
          border-radius: 4px;
          border: 2px solid #B6B09F;
          transition: all 0.2s ease;
          cursor: pointer;
          accent-color: #000000;
        }

        .checkbox-custom:checked {
          background-color: #000000;
          border-color: #000000;
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
          font-size: 0.95rem;
        }

        .input-group {
          margin-bottom: 1.5rem;
          animation: fadeInUp 0.8s ease-out backwards;
        }

        .input-group:nth-child(2) { animation-delay: 0.1s; }
        .input-group:nth-child(3) { animation-delay: 0.2s; }
        .input-group:nth-child(4) { animation-delay: 0.3s; }

        .error-message {
          background-color: #fee2e2;
          border: 1px solid #dc2626;
          color: #dc2626;
          padding: 0.75rem;
          border-radius: 8px;
          margin-bottom: 1rem;
          font-size: 0.875rem;
          animation: fadeInUp 0.3s ease-out;
        }

        .spinner {
          border: 2px solid #EAE4D5;
          border-top: 2px solid transparent;
          border-radius: 50%;
          width: 16px;
          height: 16px;
          animation: spin 0.8s linear infinite;
          display: inline-block;
          margin-right: 8px;
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>

      <div className="max-w-md w-full space-y-8 p-8 rounded-lg" style={{ backgroundColor: '#ffffff' }}>
        <div className="login-container">
          <h1 className="title-text">Welcome Back</h1>
          <p className="subtitle-text">Sign in to continue your journey</p>

          <form onSubmit={handleSubmit}>
            {/* Error Message */}
            {error && (
              <div className="error-message">
                <strong>Error:</strong> {error}
              </div>
            )}

            {/* Email Address */}
            <div className="input-group">
              <label htmlFor="email" className="form-label">
                Email Address
              </label>
              <input
                id="email"
                className={`form-input block w-full ${error ? 'error' : ''}`}
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                autoFocus
                autoComplete="username"
                placeholder="Enter your email"
                disabled={loading}
              />
            </div>

            {/* Password */}
            <div className="input-group">
              <label htmlFor="password" className="form-label">
                Password
              </label>
              <input
                id="password"
                className={`form-input block w-full ${error ? 'error' : ''}`}
                type="password"
                name="password"
                value={formData.password}
                onChange={handleChange}
                required
                autoComplete="current-password"
                placeholder="Enter your password"
                disabled={loading}
              />
            </div>

            {/* Remember Me */}
            <div className="input-group flex items-center justify-between">
              <label htmlFor="remember_me" className="inline-flex items-center cursor-pointer">
                <input
                  id="remember_me"
                  type="checkbox"
                  className="checkbox-custom"
                  name="remember"
                  checked={formData.remember}
                  onChange={handleChange}
                  disabled={loading}
                />
                <span className="ml-2 text-sm" style={{ color: '#000000' }}>
                  Remember me
                </span>
              </label>

              <Link to="/forgot-password" className="forgot-link">
                Forgot password?
              </Link>
            </div>

            <button
              type="submit"
              className="login-button w-full"
              style={{ animation: 'fadeInUp 0.8s ease-out 0.4s backwards' }}
              disabled={loading}
            >
              {loading ? (
                <>
                  <span className="spinner"></span>
                  Signing In...
                </>
              ) : (
                'Sign In'
              )}
            </button>

            <div
              style={{
                textAlign: 'center',
                marginTop: '1.5rem',
                animation: 'fadeInUp 0.8s ease-out 0.5s backwards',
              }}
            >
              <span className="text-sm" style={{ color: '#B6B09F' }}>
                Don't have an account?{' '}
              </span>
              <Link to="/register" className="forgot-link font-semibold">
                Create one
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default Login;