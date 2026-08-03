import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.rehalivan.edulearn', 
  appName: 'Eduvan Marketplace',
  webDir: 'www',
  plugins: {
    SplashScreen: {
      launchShowDuration: 1000,
      launchAutoHide: true,
      backgroundColor: '#111827',
      androidSplashResourceName: 'transparent',
      splashImmersive: true,
    },
  },
};

export default config;
