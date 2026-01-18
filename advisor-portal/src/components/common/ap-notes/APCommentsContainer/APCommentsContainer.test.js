import React from 'react';
import ReactDOM from 'react-dom';
import APCommentsContainer from './APCommentsContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APCommentsContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});