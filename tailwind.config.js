import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {

    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      ],
      safelist: [
    'w-full',
    'max-w-lg',
    'mx-auto',
    'mt-3',
    'flex',
    'justify-between',
    'text-sm',
    'font-medium',
    'mb-1',
    'text-blue-600',
    'text-red-600',
    'relative',
    'h-2',
    'h-3',
    'bg-gray-200',
    'rounded-full',
    'overflow-hidden',
    'shadow-inner',
    'absolute',
    'top-0',
    'left-1/2',
    'transition-all',
    'duration-700',
    // Tooltip
    'bg-gray-800',
    'text-white',
    'text-xs',
    'px-2',
    'py-1',
    'rounded',
    'opacity-0',
    'pointer-events-none',
    'transition-opacity',
    'duration-200',
    'animate-grow'
  ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
