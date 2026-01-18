import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageDocuments from './LitigationCasePageDocuments';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageDocuments />, div);
  ReactDOM.unmountComponentAtNode(div);
});