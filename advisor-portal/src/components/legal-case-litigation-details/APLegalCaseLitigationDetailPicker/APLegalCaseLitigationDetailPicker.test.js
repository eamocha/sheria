import React from 'react';
import ReactDOM from 'react-dom';
import APLegalCaseLitigationDetailPicker from './APLegalCaseLitigationDetailPicker';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APLegalCaseLitigationDetailPicker />, div);
  ReactDOM.unmountComponentAtNode(div);
});