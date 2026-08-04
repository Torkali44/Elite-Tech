<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
  tailwind.config = {
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
      }
    }
  }
</script>
<style>
  [x-cloak] { display: none !important; }
  html { scroll-behavior: smooth; -webkit-tap-highlight-color: transparent; }
  body {
    font-family: 'Cairo', system-ui, sans-serif;
    background: #F7FAFC;
    color: #2D3748;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  ::-webkit-scrollbar { width: 6px; height: 6px; }
  ::-webkit-scrollbar-track { background: #F7FAFC; }
  ::-webkit-scrollbar-thumb { background: #CBD5E0; border-radius: 999px; }
  ::-webkit-scrollbar-thumb:hover { background: #A0AEC0; }

  .btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: #1A365D;
    color: #fff; font-weight: 700;
    padding: 0.7rem 1.35rem; border-radius: 0.5rem;
    transition: background 0.2s ease;
    min-height: 44px;
    border: none;
  }
  .btn-primary:hover { background: #102A43; }

  .btn-secondary {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: #F6993F;
    color: #fff; font-weight: 700;
    padding: 0.7rem 1.35rem; border-radius: 0.5rem;
    transition: background 0.2s ease;
    min-height: 44px;
    border: none;
  }
  .btn-secondary:hover { background: #DD6B20; }

  .btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    border: 1.5px solid #1A365D; color: #1A365D; font-weight: 700;
    padding: 0.65rem 1.25rem; border-radius: 0.5rem;
    background: transparent; transition: all 0.2s ease;
    min-height: 44px;
  }
  .btn-outline:hover { background: #1A365D; color: #fff; }

  .btn-ghost {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    color: #4A5568; font-weight: 600; padding: 0.55rem 1rem; border-radius: 0.5rem;
    transition: background 0.15s ease;
    min-height: 44px;
  }
  .btn-ghost:hover { background: #EDF2F7; color: #1A365D; }

  .card {
    background: #ffffff;
    border-radius: 0.75rem;
    border: 1px solid #E2E8F0;
    box-shadow: 0 1px 3px rgba(26, 54, 93, 0.04);
  }

  .card-hover:hover {
    border-color: #CBD5E0;
  }

  .input {
    width: 100%; border-radius: 0.5rem; border: 1.5px solid #E2E8F0;
    background: #F7FAFC; padding: 0.7rem 1rem; font-size: 0.95rem;
    outline: none; transition: border-color 0.15s ease, box-shadow 0.15s ease;
    min-height: 44px;
  }
  .input:focus {
    border-color: #1A365D;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
  }

  .badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.7rem; border-radius: 0.375rem;
    font-size: 0.75rem; font-weight: 700;
    line-height: 1.2;
  }

  .side-link {
    display: flex; align-items: center; gap: 0.7rem;
    padding: 0.7rem 1rem; border-radius: 0.5rem;
    font-size: 0.9rem; color: #4A5568; transition: background 0.15s ease, color 0.15s ease;
    min-height: 42px;
  }
  .side-link:hover { background: #F7FAFC; color: #1A365D; }
  .side-link.active { background: #EBF4FF; color: #1A365D; font-weight: 700; }

  .hero-mesh {
    background: #1A365D;
  }
  .hero-grid { display: none; }

  .section-title { font-weight: 800; color: #1A365D; line-height: 1.25; }
  .section-sub { color: #4A5568; line-height: 1.7; font-size: 1.05rem; }

  .gate-backdrop {
    background: rgba(26, 54, 93, 0.55);
  }

  @media print {
    .no-print { display: none !important; }
    body { background: #fff; }
  }
</style>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/partials/styles.blade.php ENDPATH**/ ?>