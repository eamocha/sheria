import React from 'react';
import ReactDOM from 'react-dom';
import AttachmentItemData from './AttachmentItemData';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AttachmentItemData />, div);
  ReactDOM.unmountComponentAtNode(div);
});