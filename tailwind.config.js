/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1A365D',
          50: '#F0F4F8',
          100: '#D9E2EC',
          200: '#BCCCDC',
          300: '#9FB3C8',
          500: '#243B53',
          600: '#1A365D',
          700: '#102A43',
          800: '#0A1929',
        },
        secondary: {
          DEFAULT: '#F6993F',
          50: '#FFF8F1',
          100: '#FEECDC',
          500: '#F6993F',
          600: '#DD6B20',
          700: '#C05621',
        },
        tertiary: '#4A5568',
        neutral: '#F7FAFC',
        mist: '#E2E8F0',
        ink: '#1A202C',
      },
      fontFamily: {
        sans: ['Cairo', 'system-ui', 'sans-serif'],
        display: ['Cairo', 'sans-serif'],
      },
      boxShadow: {
        card: '0 1px 3px rgba(26, 54, 93, 0.06)',
        soft: '0 1px 2px rgba(26, 54, 93, 0.04)',
        'card-hover': '0 2px 8px rgba(26, 54, 93, 0.08)',
        glow: 'none',
        navy: '0 2px 8px rgba(26, 54, 93, 0.1)',
        glass: '0 1px 3px rgba(26, 54, 93, 0.06)',
      },
      animation: {
        'fade-up': 'fadeUp 0.4s ease-out both',
        'fade-in': 'fadeIn 0.3s ease-out both',
      },
      keyframes: {
        fadeUp: { '0%': { opacity: '0', transform: 'translateY(12px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
      },
    },
  },
  plugins: [],
}
