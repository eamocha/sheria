import React from 'react';
import ReactDOM from 'react-dom';
import FileVersionsRow from './FileVersionsRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<FileVersionsRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});