import React from 'react';
import ReactDOM from 'react-dom';
import APPickLegalCaseLitigationDetail from './APPickLegalCaseLitigationDetail';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPickLegalCaseLitigationDetail />, div);
  ReactDOM.unmountComponentAtNode(div);
});