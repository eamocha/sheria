import React from 'react';
import ReactDOM from 'react-dom';
import AttachmentItem from './AttachmentItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AttachmentItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});