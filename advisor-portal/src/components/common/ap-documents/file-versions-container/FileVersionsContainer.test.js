import React from 'react';
import ReactDOM from 'react-dom';
import FileVersionsContainer from './FileVersionsContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<FileVersionsContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});