import React from 'react';
import ReactDOM from 'react-dom';
import APMultiFileUploadInput from './APMultiFileUploadInput';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMultiFileUploadInput />, div);
  ReactDOM.unmountComponentAtNode(div);
});