import React from 'react';
import ReactDOM from 'react-dom';
import APMainMenuAddItem from './APMainMenuAddItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMainMenuAddItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});