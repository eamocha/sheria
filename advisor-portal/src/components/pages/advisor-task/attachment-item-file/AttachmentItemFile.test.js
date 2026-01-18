import React from 'react';
import ReactDOM from 'react-dom';
import AttachmentItemFile from './AttachmentItemFile';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AttachmentItemFile />, div);
  ReactDOM.unmountComponentAtNode(div);
});