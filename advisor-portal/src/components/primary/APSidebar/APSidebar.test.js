import React from 'react';
import ReactDOM from 'react-dom';
import APSidebar from './APSidebar';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APSidebar />, div);
  ReactDOM.unmountComponentAtNode(div);
});