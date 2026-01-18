import React from 'react';
import ReactDOM from 'react-dom';
import APMainMenuBottomItems from './APMainMenuBottomItems';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMainMenuBottomItems />, div);
  ReactDOM.unmountComponentAtNode(div);
});