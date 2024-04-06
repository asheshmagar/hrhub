import React from 'react';
import { createRoot } from 'react-dom/client';
import { App } from './App';
import './global.pcss';

const root = createRoot(document.getElementById('hrhub')!);

root.render(<App />);
