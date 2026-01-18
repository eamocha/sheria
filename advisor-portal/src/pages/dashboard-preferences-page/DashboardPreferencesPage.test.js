import React from 'react';
import ReactDOM from 'react-dom';
import DashboardPreferencesPage from './DashboardPreferencesPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<DashboardPreferencesPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});