import React from 'react';
import ReactDOM from 'react-dom';
import APPageActionsToolbar from './APPageActionsToolbar';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPageActionsToolbar />, div);
  ReactDOM.unmountComponentAtNode(div);
});