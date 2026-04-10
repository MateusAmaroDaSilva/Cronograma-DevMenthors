const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'teacher-tag': {
                    DEFAULT: 'hsl(230, 60%, 94%)',
                    text: 'hsl(230, 60%, 35%)',
                    border: 'hsl(230, 60%, 85%)',
                },
                'mentor-tag': {
                    DEFAULT: 'hsl(160, 40%, 92%)',
                    text: 'hsl(160, 30%, 30%)',
                    border: 'hsl(160, 40%, 80%)',
                },
            },
            backgroundColor: {
                'primary': 'hsl(230, 90%, 50%)',
                'secondary': 'hsl(225, 60%, 95%)',
                'muted': 'hsl(220, 14%, 94%)',
                'accent': 'hsl(230, 80%, 93%)',
            },
            textColor: {
                'primary': 'hsl(230, 90%, 50%)',
                'secondary': 'hsl(230, 90%, 40%)',
                'muted': 'hsl(220, 10%, 46%)',
                'destructive': 'hsl(0, 84%, 60%)',
            },
            borderColor: {
                'primary': 'hsl(220, 13%, 89%)',
                'ring': 'hsl(230, 90%, 50%)',
            },
            boxShadow: {
                'sm': '0 1px 3px rgba(30, 50, 120, 0.06), 0 0 0 1px rgba(30, 50, 120, 0.04)',
                'md': '0 4px 12px rgba(30, 50, 120, 0.08), 0 0 0 1px rgba(30, 50, 120, 0.04)',
                'lg': '0 8px 24px rgba(30, 50, 120, 0.1), 0 0 0 1px rgba(30, 50, 120, 0.04)',
            },
            borderRadius: {
                'DEFAULT': '0.75rem',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
