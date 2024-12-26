export function removeAccents(text: string): string {
  // Tách dấu và chữ cái ra
  const decomposedText = text.normalize('NFD');

  // Xóa hết các kí tự thể hiện dấu
  const normalizedText = decomposedText.replace(/[^\x00-\x7F]/g, '');

  // Trả về chuỗi không dấu
  return normalizedText;
}
