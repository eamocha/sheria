import React from 'react';
import ReactDOM from 'react-dom';
import DropzoneContainer from './DropzoneContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<MenuButton />, div);
  ReactDOM.unmountComponentAtNode(div);
});