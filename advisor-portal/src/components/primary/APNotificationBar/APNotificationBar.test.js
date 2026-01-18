import React from 'react';
import ReactDOM from 'react-dom';
import APNotificationBar from './APNotificationBar';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APNotificationBar />, div);
  ReactDOM.unmountComponentAtNode(div);
});