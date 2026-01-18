import React from 'react';
import ReactDOM from 'react-dom';
import HearingsPage from './HearingsPage';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<HearingsPage />, div);
  ReactDOM.unmountComponentAtNode(div);
});