import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const Register = () => {
  const navigate = useNavigate();
  
  const [formData, setFormData] = useState({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [successMessage, setSuccessMessage] = useState('');

  // Your Laravel API URL
  const API_BASE_URL = 'http://localhost:8000/api';

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    // Clear specific field error when user starts typing
    if (errors[name]) {
      setErrors((prev) => ({
        ...prev,
        [name]: null,
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setSuccessMessage('');

    console.log('🔄 Starting registration process...');
    console.log('📝 Form data:', { ...formData, password: '***' });

    try {
      const response = await fetch(`${API_BASE_URL}/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      console.log('📥 Response status:', response.status);

      const data = await response.json();
      console.log('📦 Response data:', data);

      if (!response.ok) {
        // Handle validation errors
        if (data.errors) {
          console.log('❌ Validation errors:', data.errors);
          setErrors(data.errors);
          return;
        }
        throw new Error(data.message || 'Registration failed. Please try again.');
      }

      if (data.success) {
        console.log('✅ Registration successful!');
        
        // Store authentication token
        localStorage.setItem('auth_token', data.token);
        console.log('💾 Token saved');
        
        // Store user data
        localStorage.setItem('user', JSON.stringify(data.user));
        console.log('💾 User saved:', data.user);

        setSuccessMessage('Account created successfully! Redirecting...');

        // Redirect to profile after a short delay
        setTimeout(() => {
          navigate('/profile');
        }, 1500);
      }
    } catch (err) {
      console.error('❌ Registration error:', err);
      setErrors({ general: err.message || 'An error occurred. Please try again.' });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    // Add smooth focus animations
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach((input) => {
      input.addEventListener('focus', function () {
        this.parentElement.style.transform = 'scale(1.01)';
      });

      input.addEventListener('blur', function () {
        this.parentElement.style.transform = 'scale(1)';
      });
    });
  }, []);

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

        .register-container {
          animation: fadeInUp 0.8s ease-out;
          position: relative;
          overflow: hidden;
        }

        .register-container::before {
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
          width: 100%;
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
        }

        .form-label {
          font-weight: 600;
          color: #000000;
          margin-bottom: 0.5rem;
          font-size: 0.875rem;
          letter-spacing: 0.3px;
          display: block;
        }

        .register-button {
          background-color: #000000;
          color: #EAE4D5;
          padding: 0.875rem 2rem;
          border-radius: 8px;
          font-weight: 600;
          transition: all 0.3s ease;
          border: none;
          cursor: pointer;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
          width: 100%;
        }

        .register-button:hover:not(:disabled) {
          background-color: #1a1a1a;
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .register-button:active:not(:disabled) {
          transform: translateY(0);
        }

        .register-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        .login-link {
          color: #000000;
          font-size: 0.875rem;
          transition: all 0.3s ease;
          text-decoration: none;
          font-weight: 600;
          border-bottom: 1px solid transparent;
        }

        .login-link:hover {
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
          font-size: 0.95rem;
        }

        .input-group {
          margin-bottom: 1.25rem;
          animation: fadeInUp 0.8s ease-out backwards;
        }

        .input-group:nth-child(2) { animation-delay: 0.1s; }
        .input-group:nth-child(3) { animation-delay: 0.2s; }
        .input-group:nth-child(4) { animation-delay: 0.3s; }
        .input-group:nth-child(5) { animation-delay: 0.4s; }
        .input-group:nth-child(6) { animation-delay: 0.5s; }

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

        .success-message {
          background-color: #d1fae5;
          border: 1px solid #10b981;
          color: #065f46;
          padding: 0.75rem;
          border-radius: 8px;
          margin-bottom: 1rem;
          font-size: 0.875rem;
          animation: fadeInUp 0.3s ease-out;
        }

        .field-error {
          color: #dc2626;
          font-size: 0.75rem;
          margin-top: 0.25rem;
          animation: shake 0.4s ease-in-out;
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
        <div className="register-container">
          <h1 className="title-text">Join Us Today</h1>
          <p className="subtitle-text">Create your account and start connecting</p>

          <form onSubmit={handleSubmit}>
            {/* General Error Message */}
            {errors.general && (
              <div className="error-message">
                <strong>Error:</strong> {errors.general}
              </div>
            )}

            {/* Success Message */}
            {successMessage && (
              <div className="success-message">
                <strong>Success:</strong> {successMessage}
              </div>
            )}

            {/* Name */}
            <div className="input-group">
              <label htmlFor="name" className="form-label">
                Full Name
              </label>
              <input
                id="name"
                className={`form-input ${errors.name ? 'error' : ''}`}
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                required
                autoFocus
                autoComplete="name"
                placeholder="John Doe"
                disabled={loading}
              />
              {errors.name && (
                <p className="field-error">{errors.name[0]}</p>
              )}
            </div>

            {/* Username */}
            <div className="input-group">
              <label htmlFor="username" className="form-label">
                Username
              </label>
              <input
                id="username"
                className={`form-input ${errors.username ? 'error' : ''}`}
                type="text"
                name="username"
                value={formData.username}
                onChange={handleChange}
                autoComplete="username"
                placeholder="johndoe"
                disabled={loading}
              />
              {errors.username && (
                <p className="field-error">{errors.username[0]}</p>
              )}
              <p className="text-xs mt-1" style={{ color: '#B6B09F' }}>
                Optional - A unique username for your profile
              </p>
            </div>

            {/* Email Address */}
            <div className="input-group">
              <label htmlFor="email" className="form-label">
                Email Address
              </label>
              <input
                id="email"
                className={`form-input ${errors.email ? 'error' : ''}`}
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                autoComplete="email"
                placeholder="john@example.com"
                disabled={loading}
              />
              {errors.email && (
                <p className="field-error">{errors.email[0]}</p>
              )}
            </div>

            {/* Password */}
            <div className="input-group">
              <label htmlFor="password" className="form-label">
                Password
              </label>
              <input
                id="password"
                className={`form-input ${errors.password ? 'error' : ''}`}
                type="password"
                name="password"
                value={formData.password}
                onChange={handleChange}
                required
                autoComplete="new-password"
                placeholder="Create a strong password (min 8 characters)"
                disabled={loading}
              />
              {errors.password && (
                <p className="field-error">{errors.password[0]}</p>
              )}
            </div>

            {/* Confirm Password */}
            <div className="input-group">
              <label htmlFor="password_confirmation" className="form-label">
                Confirm Password
              </label>
              <input
                id="password_confirmation"
                className={`form-input ${errors.password_confirmation ? 'error' : ''}`}
                type="password"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleChange}
                required
                autoComplete="new-password"
                placeholder="Re-enter your password"
                disabled={loading}
              />
              {errors.password_confirmation && (
                <p className="field-error">{errors.password_confirmation[0]}</p>
              )}
            </div>

            <button
              type="submit"
              className="register-button"
              style={{ animation: 'fadeInUp 0.8s ease-out 0.6s backwards' }}
              disabled={loading}
            >
              {loading ? (
                <>
                  <span className="spinner"></span>
                  Creating Account...
                </>
              ) : (
                'Create Account'
              )}
            </button>

            <div
              style={{
                textAlign: 'center',
                marginTop: '1.5rem',
                animation: 'fadeInUp 0.8s ease-out 0.7s backwards',
              }}
            >
              <span className="text-sm" style={{ color: '#B6B09F' }}>
                Already have an account?{' '}
              </span>
              <Link to="/login" className="login-link">
                Sign in
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default Register;